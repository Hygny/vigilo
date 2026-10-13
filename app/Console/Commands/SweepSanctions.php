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
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
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
 *  - ritmo: no máximo `risco.sweep.per_minute` empresas/min (dorme só o que
 *    sobrar do intervalo, sem somar latência ao tempo da própria consulta);
 *  - disjuntor: N empresas SEGUIDAS com a CGU indisponível → aborta (evita
 *    esperar o timeout em toda a carteira quando a API está fora);
 *  - empresa sem coleta ainda é pulada (o evento precisa de um snapshot);
 *  - erro inesperado numa empresa é reportado e a varredura segue.
 */
final class SweepSanctions extends Command
{
    protected $signature = 'vigilo:varrer-sancoes
        {--per-minute= : Teto de empresas/min (padrão: risco.sweep.per_minute; 0 = sem pausa)}';

    protected $description = 'Varre as sanções (CGU) da carteira inteira e alerta as sanções novas.';

    public function handle(PortalTransparenciaClient $client, SanctionMonitor $sanctions, CompanyAlertNotifier $notifier): int
    {
        if (! $client->isConfigured()) {
            $this->warn('Integração com a CGU não configurada (PORTAL_TRANSPARENCIA_API_KEY) — nada a varrer.');

            return self::SUCCESS;
        }

        $option = $this->option('per-minute');
        $perMinute = $option !== null ? (int) $option : (int) config('risco.sweep.per_minute');
        $intervalo = $perMinute > 0 ? 60 / $perMinute : 0.0; // segundos por empresa
        $maxFalhas = max(1, (int) config('risco.sweep.max_consecutive_failures'));

        $checadas = 0;
        $alertas = 0;
        $semColeta = 0;
        $falhasSeguidas = 0;
        $abortou = false;

        MonitoredCompany::query()
            ->with(['latestSnapshot', 'portfolio.organization.users'])
            ->chunkById(200, function (EloquentCollection $companies) use ($sanctions, $notifier, $intervalo, $maxFalhas, &$checadas, &$alertas, &$semColeta, &$falhasSeguidas, &$abortou): bool {
                /** @var EloquentCollection<int, MonitoredCompany> $companies */
                foreach ($companies as $company) {
                    $snapshot = $company->latestSnapshot;

                    if ($snapshot === null) {
                        $semColeta++; // sem coleta ainda: nada a ligar o evento; pega na próxima

                        continue;
                    }

                    $inicio = microtime(true);

                    try {
                        $novas = $sanctions->sync($company);
                    } catch (Throwable $e) {
                        report($e); // erro inesperado (não é "CGU fora"): registra e segue

                        continue;
                    }

                    if ($novas === null) {
                        if (++$falhasSeguidas >= $maxFalhas) {
                            $abortou = true;

                            return false; // para o chunkById
                        }
                    } else {
                        $falhasSeguidas = 0;
                        $checadas++;

                        if ($novas !== []) {
                            $events = $this->persistEvents($company, $snapshot, $novas);
                            $notifier->notify($company, $snapshot->razao_social ?? $company->formattedCnpj(), $events);
                            $alertas += $events->count();
                        }
                    }

                    $this->pace($inicio, $intervalo);
                }

                return true;
            });

        if ($abortou) {
            $this->error("CGU indisponível em {$maxFalhas} empresa(s) seguida(s) — varredura abortada (tenta de novo na próxima). Checadas até aqui: {$checadas}.");

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Varredura de sanções: %d empresa(s) checada(s), %d alerta(s) de sanção nova%s.',
            $checadas,
            $alertas,
            $semColeta > 0 ? ", {$semColeta} sem coleta ainda (puladas)" : '',
        ));

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
