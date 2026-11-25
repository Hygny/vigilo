<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTO\Risco\EmpresaLigadaRisco;
use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Models\ChangeEvent;
use App\Models\CompanySnapshot;
use App\Models\MaintenanceRun;
use App\Models\MonitoredCompany;
use App\Services\Alerts\CompanyAlertNotifier;
use App\Services\Risco\RiscoAoRedorMonitor;
use App\Support\Cnpj;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Varredura diária do "risco ao redor" da carteira: para cada empresa
 * monitorada, compara os sinais de risco das empresas LIGADAS a ela (sócio PJ,
 * participação, grupo pelo sócio PJ) com os da rodada anterior; sinal novo
 * (Lista Suja, sanção vigente, dívida ativa, situação negativa) vira alerta na
 * Inbox — com notificação e webhook, como os demais.
 *
 * Fontes locais (grafo e PGFN na base CNPJ, cópia da CGU, Lista Suja): sem API
 * externa. Retomável: sem `--all`, pula quem já foi checado hoje. Disjuntor:
 * N falhas seguidas (base CNPJ fora) abortam a rodada.
 */
final class SweepRiscoAoRedor extends Command
{
    protected $signature = 'vigilo:varrer-risco-ao-redor
        {--all : Checa de novo também quem já foi checado hoje}';

    protected $description = 'Alerta quando uma empresa ligada a uma da carteira ganha sinal de risco (Lista Suja, sanção, dívida ativa, situação).';

    public function handle(RiscoAoRedorMonitor $monitor, CompanyAlertNotifier $notifier): int
    {
        $inicio = now();
        $maxFalhas = max(1, (int) config('risco.sweep.max_consecutive_failures'));
        $stats = ['checadas' => 0, 'alertas' => 0, 'sem_coleta' => 0, 'incompletas' => 0, 'erros' => 0];
        $falhasSeguidas = 0;
        $abortou = false;

        $this->query()->chunkById(200, function (EloquentCollection $companies) use ($monitor, $notifier, $maxFalhas, &$stats, &$falhasSeguidas, &$abortou): bool {
            /** @var EloquentCollection<int, MonitoredCompany> $companies */
            foreach ($companies as $company) {
                $snapshot = $company->latestSnapshot;

                if ($snapshot === null) {
                    $stats['sem_coleta']++;

                    continue;
                }

                try {
                    $report = $monitor->consultar($company); // grafo + lotes, fora de transação

                    if ($report === null) {
                        $stats['incompletas']++;
                        $falhasSeguidas++;
                    } else {
                        // Flags e alertas no MESMO commit: nunca fica sinal "conhecido"
                        // sem o alerta (a transação do aplicar() vira savepoint).
                        $events = DB::transaction(fn (): EloquentCollection => $this->persistEvents($company, $snapshot, $monitor->aplicar($company, $report)));
                        $falhasSeguidas = 0;
                        $stats['checadas']++;
                        $stats['alertas'] += $events->count();

                        $notifier->notify($company, $snapshot->razao_social, $events);
                    }
                } catch (Throwable $e) {
                    report($e);
                    $stats['erros']++;
                    $falhasSeguidas++;
                }

                if ($falhasSeguidas >= $maxFalhas) {
                    $abortou = true;

                    return false;
                }
            }

            return true;
        });

        $resumo = sprintf(
            '%d empresa(s) checada(s), %d alerta(s) de risco em empresa ligada, %d sem coleta ainda, %d com fonte indisponível, %d com erro',
            $stats['checadas'], $stats['alertas'], $stats['sem_coleta'], $stats['incompletas'], $stats['erros'],
        );

        if ($abortou) {
            $this->error("{$maxFalhas} falha(s) seguida(s) — varredura abortada; a próxima retoma de onde parou. {$resumo}.");
            MaintenanceRun::registrar(MaintenanceRun::KIND_RISCO_AO_REDOR, false, "Abortada após {$maxFalhas} falhas seguidas. {$resumo}.", $inicio);

            return self::FAILURE;
        }

        $this->info("Risco ao redor: {$resumo}.");
        MaintenanceRun::registrar(MaintenanceRun::KIND_RISCO_AO_REDOR, true, ucfirst($resumo).'.', $inicio);

        return self::SUCCESS;
    }

    /** @return Builder<MonitoredCompany> */
    private function query(): Builder
    {
        // Mesmo recorte da varredura de sanções (sem raw_json do snapshot).
        $query = MonitoredCompany::query()->with([
            'latestSnapshot:company_snapshots.id,company_snapshots.monitored_company_id,company_snapshots.captured_at,company_snapshots.razao_social',
            'portfolio.organization.users',
        ]);

        if (! $this->option('all')) {
            $inicioDoDia = Carbon::now((string) config('risco.sweep.timezone'))
                ->startOfDay()
                ->setTimezone((string) config('app.timezone'));

            $query->where(fn (Builder $q) => $q
                ->whereNull('neighbor_risk_synced_at')
                ->orWhere('neighbor_risk_synced_at', '<', $inicioDoDia));
        }

        return $query;
    }

    /**
     * Um alerta por empresa ligada com sinal novo, ligado ao snapshot mais
     * recente da empresa da carteira. Lista Suja / sanção = alta; dívida /
     * situação = média.
     *
     * @param  list<array{empresa: EmpresaLigadaRisco, novos: list<string>}>  $novidades
     * @return EloquentCollection<int, ChangeEvent>
     */
    private function persistEvents(MonitoredCompany $company, CompanySnapshot $snapshot, array $novidades): EloquentCollection
    {
        /** @var EloquentCollection<int, ChangeEvent> $events */
        $events = new EloquentCollection;

        foreach ($novidades as ['empresa' => $empresa, 'novos' => $novos]) {
            $grave = array_intersect($novos, EmpresaLigadaRisco::CODIGOS_GRAVES) !== [];

            $events->push($company->changeEvents()->create([
                'from_snapshot_id' => null,
                'to_snapshot_id' => $snapshot->id,
                'type' => ChangeType::RiscoAoRedor,
                'field' => 'risco_ao_redor',
                'old_value' => null,
                'new_value' => sprintf(
                    '%s (%s · %s): %s',
                    $empresa->nome,
                    Cnpj::tryFrom($empresa->cnpj)?->formatted() ?? $empresa->cnpj,
                    $empresa->ligacao,
                    implode(', ', array_map(fn (string $c): string => $empresa->rotulo($c), $novos)),
                ),
                'severity' => $grave ? Severity::High : Severity::Medium,
                'detected_at' => now(),
            ]));
        }

        return $events;
    }
}
