<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Models\ChangeEvent;
use App\Models\CompanySnapshot;
use App\Models\MaintenanceRun;
use App\Models\MonitoredCompany;
use App\Services\Alerts\CompanyAlertNotifier;
use App\Services\Bens\DevedorMonitor;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Varredura diária das DEVEDORAS monitoradas (recuperação de crédito): para
 * cada empresa da carteira marcada como devedora, compara bens (aeronaves,
 * participações), créditos (contratos federais vigentes) e possíveis sucessoras
 * com a rodada anterior; o que é NOVO vira alerta na Inbox — com notificação e
 * webhook, como os demais.
 *
 * Retomável: sem `--all`, pula quem já foi checada hoje. Disjuntor: N falhas
 * seguidas (base CNPJ fora) abortam a rodada.
 */
final class SweepDevedores extends Command
{
    protected $signature = 'vigilo:varrer-devedores
        {--all : Checa de novo também quem já foi checada hoje}';

    protected $description = 'Alerta quando uma devedora monitorada ganha bem, crédito a receber ou possível sucessora nova.';

    /** Agenda da varredura (fonte única: routes/console.php, card Rotinas e card da devedora). */
    public const CRON = '0 8,13 * * *';

    public const HORARIOS = 'todo dia às 08:00 e 13:00';

    /** Rótulo do alerta por tipo de item. */
    private const ROTULOS = [
        DevedorMonitor::AERONAVE => 'Aeronave',
        DevedorMonitor::PARTICIPACAO => 'Participação societária',
        DevedorMonitor::CONTRATO => 'Contrato federal vigente',
        DevedorMonitor::SUCESSORA => 'Possível sucessora',
        DevedorMonitor::MARCA => 'Marca (INPI)',
    ];

    public function handle(DevedorMonitor $monitor, CompanyAlertNotifier $notifier): int
    {
        $inicio = now();
        $maxFalhas = max(1, (int) config('risco.sweep.max_consecutive_failures'));
        $stats = ['checadas' => 0, 'alertas' => 0, 'sem_coleta' => 0, 'indisponiveis' => 0, 'erros' => 0];
        $falhasSeguidas = 0;
        $abortou = false;

        $this->query()->chunkById(100, function (EloquentCollection $companies) use ($monitor, $notifier, $maxFalhas, &$stats, &$falhasSeguidas, &$abortou): bool {
            /** @var EloquentCollection<int, MonitoredCompany> $companies */
            foreach ($companies as $company) {
                $snapshot = $company->latestSnapshot;

                if ($snapshot === null) {
                    $stats['sem_coleta']++;

                    continue;
                }

                try {
                    $consulta = $monitor->consultar($company); // base CNPJ + RAB + Portal, fora de transação

                    // Itens conhecidos e alertas no MESMO commit. Os tipos completos são
                    // comparados mesmo se outra fonte falhou; aí a rodada não vale como
                    // checagem do dia (a 2ª passada tenta de novo) e conta para o disjuntor.
                    $events = DB::transaction(fn (): EloquentCollection => $this->persistEvents($company, $snapshot, $monitor->aplicar($company, $consulta)));
                    $stats['alertas'] += $events->count();
                    $notifier->notify($company, $snapshot->razao_social, $events);

                    if ($consulta->falhou) {
                        $stats['indisponiveis']++;
                        $falhasSeguidas++;
                    } else {
                        $stats['checadas']++;
                        $falhasSeguidas = 0;
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
            '%d devedora(s) checada(s), %d alerta(s) de bem/crédito/sucessora, %d sem coleta ainda, %d com fonte indisponível (repete na próxima passada), %d com erro',
            $stats['checadas'], $stats['alertas'], $stats['sem_coleta'], $stats['indisponiveis'], $stats['erros'],
        );

        if ($abortou) {
            $this->error("{$maxFalhas} falha(s) seguida(s) — varredura abortada; a próxima retoma de onde parou. {$resumo}.");
            MaintenanceRun::registrar(MaintenanceRun::KIND_DEVEDORES, false, "Abortada após {$maxFalhas} falhas seguidas. {$resumo}.", $inicio);

            return self::FAILURE;
        }

        $this->info("Devedoras: {$resumo}.");
        MaintenanceRun::registrar(MaintenanceRun::KIND_DEVEDORES, true, ucfirst($resumo).'.', $inicio);

        return self::SUCCESS;
    }

    /** @return Builder<MonitoredCompany> */
    private function query(): Builder
    {
        $query = MonitoredCompany::query()
            ->whereNotNull('recuperacao_desde')
            ->with([
                'latestSnapshot:company_snapshots.id,company_snapshots.monitored_company_id,company_snapshots.captured_at,company_snapshots.razao_social',
                'portfolio.organization.users',
            ]);

        if (! $this->option('all')) {
            $inicioDoDia = Carbon::now((string) config('risco.sweep.timezone'))
                ->startOfDay()
                ->setTimezone((string) config('app.timezone'));

            $query->where(fn (Builder $q) => $q
                ->whereNull('recuperacao_synced_at')
                ->orWhere('recuperacao_synced_at', '<', $inicioDoDia));
        }

        return $query;
    }

    /**
     * Um alerta por item novo, ligado ao snapshot mais recente da devedora.
     * Aeronave sem ônus e sucessora forte = alta; o resto = média.
     *
     * @param  list<array{tipo: string, chave: string, descricao: string, grave: bool}>  $novidades
     * @return EloquentCollection<int, ChangeEvent>
     */
    private function persistEvents(MonitoredCompany $company, CompanySnapshot $snapshot, array $novidades): EloquentCollection
    {
        /** @var EloquentCollection<int, ChangeEvent> $events */
        $events = new EloquentCollection;

        foreach ($novidades as $n) {
            $events->push($company->changeEvents()->create([
                'from_snapshot_id' => null,
                'to_snapshot_id' => $snapshot->id,
                'type' => $n['tipo'] === DevedorMonitor::SUCESSORA ? ChangeType::SucessoraNova : ChangeType::BemNovo,
                'field' => 'recuperacao_'.$n['tipo'],
                'old_value' => null,
                'new_value' => mb_strimwidth((self::ROTULOS[$n['tipo']] ?? $n['tipo']).': '.$n['descricao'], 0, 500, '…'),
                'severity' => $n['grave'] ? Severity::High : Severity::Medium,
                'detected_at' => now(),
            ]));
        }

        return $events;
    }
}
