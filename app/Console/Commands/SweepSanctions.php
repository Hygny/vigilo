<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ChangeType;
use App\Enums\FonteSancao;
use App\Enums\Severity;
use App\Models\ChangeEvent;
use App\Models\CompanySanction;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Services\Alerts\CompanyAlertNotifier;
use App\Services\Risco\PortalTransparenciaClient;
use App\Services\Risco\SanctionMonitor;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Varredura diária de sanções (CGU) da carteira inteira: para cada empresa
 * monitorada, sincroniza as sanções conhecidas e transforma as NOVAS em alerta
 * crítico (change_event + notificação + webhook).
 *
 * Vive fora do refresh da Receita de propósito: a CGU é API externa (3 chamadas
 * por CNPJ, teto de req/min), e misturá-la ao refresh deixava a atualização da
 * carteira esperando por ela. Agendada de madrugada (routes/console.php).
 *
 * Proteções:
 *  - atômica por empresa: sanção nova + change_event no MESMO commit (nunca fica
 *    sanção "conhecida" sem o alerta); a notificação é enfileirada depois;
 *  - retomável: pula a empresa já checada hoje (`risco.sweep.timezone`), então
 *    uma 2ª passada na madrugada só pega o que a 1ª não terminou (`--all` força);
 *  - ritmo: no máximo `risco.sweep.per_minute` empresas/min (dorme só o que
 *    sobrar do intervalo, sem somar latência ao tempo da própria consulta);
 *  - disjuntor: N empresas SEGUIDAS com falha (CGU indisponível ou erro
 *    inesperado) → aborta (evita esperar o timeout em toda a carteira);
 *  - empresa sem coleta ainda é pulada (o evento precisa de um snapshot);
 *  - erro inesperado numa empresa é reportado, a transação dela é desfeita e a
 *    varredura segue para a próxima.
 *
 * Resumo e aborto vão para o log da aplicação (roda em background no scheduler,
 * sem terminal para ler a saída).
 */
final class SweepSanctions extends Command
{
    protected $signature = 'vigilo:varrer-sancoes
        {--per-minute= : Teto de empresas/min (padrão: risco.sweep.per_minute; 0 = sem pausa)}
        {--all : Varre também as empresas já checadas hoje}';

    protected $description = 'Varre as sanções (CGU) da carteira inteira e alerta as sanções novas.';

    public function handle(PortalTransparenciaClient $client, SanctionMonitor $sanctions, CompanyAlertNotifier $notifier): int
    {
        if (! $client->isConfigured()) {
            $this->warn('Integração com a CGU não configurada (PORTAL_TRANSPARENCIA_API_KEY) — nada a varrer.');

            return self::SUCCESS;
        }

        $option = $this->option('per-minute');

        if ($option !== null && ! ctype_digit($option)) {
            $this->error('--per-minute precisa ser um inteiro >= 0.');

            return self::INVALID;
        }

        $perMinute = $option !== null ? (int) $option : (int) config('risco.sweep.per_minute');
        $intervalo = $perMinute > 0 ? 60 / $perMinute : 0.0; // segundos por empresa
        $maxFalhas = max(1, (int) config('risco.sweep.max_consecutive_failures'));

        $stats = ['checadas' => 0, 'alertas' => 0, 'sem_coleta' => 0, 'indisponiveis' => 0, 'erros' => 0];
        $falhasSeguidas = 0;
        $abortou = false;

        $this->query()->chunkById(200, function (EloquentCollection $companies) use ($sanctions, $notifier, $intervalo, $maxFalhas, &$stats, &$falhasSeguidas, &$abortou): bool {
            /** @var EloquentCollection<int, MonitoredCompany> $companies */
            foreach ($companies as $company) {
                $snapshot = $company->latestSnapshot;

                if ($snapshot === null) {
                    $stats['sem_coleta']++; // sem coleta ainda: nada a ligar o evento; pega na próxima

                    continue;
                }

                $inicio = microtime(true);

                try {
                    $report = $sanctions->consultar($company); // HTTP, fora de transação

                    if ($report === null) {
                        $stats['indisponiveis']++;
                        $falhasSeguidas++;
                    } else {
                        $events = DB::transaction(function () use ($sanctions, $company, $snapshot, $report): EloquentCollection {
                            $novas = $sanctions->aplicar($company, $report);

                            return $this->persistEvents($company, $snapshot, $novas);
                        });

                        $falhasSeguidas = 0;
                        $stats['checadas']++;
                        $stats['alertas'] += $events->count();

                        // Depois do commit: o evento já está na Inbox mesmo que a entrega falhe.
                        $notifier->notify($company, $snapshot->razao_social, $events);
                    }
                } catch (Throwable $e) {
                    report($e); // erro inesperado: registra, desfaz só esta empresa e segue
                    $stats['erros']++;
                    $falhasSeguidas++;
                }

                if ($falhasSeguidas >= $maxFalhas) {
                    $abortou = true;

                    return false; // para o chunkById
                }

                $this->pace($inicio, $intervalo);
            }

            return true;
        });

        return $this->finish($stats, $abortou, $maxFalhas);
    }

    /**
     * Empresas a varrer, com só o que a varredura usa do snapshot (sem raw_json).
     * Sem `--all`, pula quem já foi checado desde o início do dia — é o que torna
     * a 2ª passada da madrugada uma retomada, não uma repetição.
     *
     * @return Builder<MonitoredCompany>
     */
    private function query(): Builder
    {
        $query = MonitoredCompany::query()->with([
            'latestSnapshot:company_snapshots.id,company_snapshots.monitored_company_id,company_snapshots.captured_at,company_snapshots.razao_social',
            'portfolio.organization.users',
        ]);

        if (! $this->option('all')) {
            $inicioDoDia = Carbon::now((string) config('risco.sweep.timezone'))
                ->startOfDay()
                ->setTimezone((string) config('app.timezone'));

            $query->where(fn (Builder $q) => $q
                ->whereNull('sanctions_synced_at')
                ->orWhere('sanctions_synced_at', '<', $inicioDoDia));
        }

        return $query;
    }

    /**
     * @param  array{checadas: int, alertas: int, sem_coleta: int, indisponiveis: int, erros: int}  $stats
     */
    private function finish(array $stats, bool $abortou, int $maxFalhas): int
    {
        $resumo = sprintf(
            '%d empresa(s) checada(s), %d alerta(s) de sanção nova, %d sem coleta ainda (puladas), %d com a CGU indisponível, %d com erro',
            $stats['checadas'],
            $stats['alertas'],
            $stats['sem_coleta'],
            $stats['indisponiveis'],
            $stats['erros'],
        );

        if ($abortou) {
            $this->error("{$maxFalhas} falha(s) seguida(s) (CGU indisponível ou erro) — varredura abortada; a próxima passada retoma de onde parou. {$resumo}.");
            Log::warning('Varredura de sanções abortada pelo disjuntor.', $stats + ['max_falhas' => $maxFalhas]);

            return self::FAILURE;
        }

        $this->info("Varredura de sanções: {$resumo}.");

        $stats['indisponiveis'] + $stats['erros'] > 0
            ? Log::warning('Varredura de sanções concluída com falhas.', $stats)
            : Log::info('Varredura de sanções concluída.', $stats);

        return self::SUCCESS;
    }

    /** Dorme só o que faltar para completar o intervalo desta empresa. */
    private function pace(float $inicio, float $intervalo): void
    {
        $restante = $intervalo - (microtime(true) - $inicio);

        if ($restante > 0) {
            usleep((int) ($restante * 1_000_000));
        }
    }

    /**
     * Sanções recém-descobertas → change_events críticos ligados ao snapshot mais
     * recente (`to_snapshot_id` é obrigatório).
     *
     * @param  list<CompanySanction>  $novas
     * @return EloquentCollection<int, ChangeEvent>
     */
    private function persistEvents(MonitoredCompany $company, CompanySnapshot $snapshot, array $novas): EloquentCollection
    {
        $now = Carbon::now();

        /** @var EloquentCollection<int, ChangeEvent> $events */
        $events = new EloquentCollection;

        foreach ($novas as $sancao) {
            $events->push($company->changeEvents()->create([
                'from_snapshot_id' => null,
                'to_snapshot_id' => $snapshot->id,
                'type' => ChangeType::SancaoAdicionada,
                'field' => 'sancao',
                'old_value' => null,
                'new_value' => $this->describe($sancao),
                'severity' => Severity::Critical,
                'detected_at' => $now,
            ]));
        }

        return $events;
    }

    private function describe(CompanySanction $sancao): string
    {
        $sigla = FonteSancao::tryFrom($sancao->fonte)?->sigla() ?? mb_strtoupper($sancao->fonte);

        return $sancao->tipo !== null ? "{$sigla} — {$sancao->tipo}" : $sigla;
    }
}
