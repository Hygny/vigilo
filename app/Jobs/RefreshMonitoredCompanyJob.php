<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\CnpjDataProvider;
use App\DTO\ChangeEventData;
use App\Enums\ChangeType;
use App\Enums\RefreshStatus;
use App\Enums\Severity;
use App\Models\ChangeEvent;
use App\Models\CompanySnapshot;
use App\Models\DirtyListEmployer;
use App\Models\MonitoredCompany;
use App\Services\Alerts\CompanyAlertNotifier;
use App\Services\CompanyDiffer;
use App\Services\Risco\DirtyListMonitor;
use App\Services\SnapshotService;
use DateTimeInterface;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Refresh a single monitored company: fetch its current cadastral data (Receita),
 * store a new snapshot, diff it against the previous one, persist any change
 * events and notify the organization.
 *
 * Só fontes LOCAIS/baratas rodam aqui (dado da Receita + Lista Suja). Sanções da
 * CGU (API externa, 3 chamadas por CNPJ) NÃO rodam no refresh: o alerta de sanção
 * nova vem da varredura diária `vigilo:varrer-sancoes`, e a página da empresa
 * consulta a CGU sob demanda. Assim o refresh da carteira não espera API externa.
 */
class RefreshMonitoredCompanyJob implements ShouldBeUnique, ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Real errors (SSL, repeated 5xx) fail the job after this many exceptions.
     * We deliberately do NOT cap `$tries`: the RateLimited middleware releases
     * over-limit jobs back to the queue, and each release counts as an attempt —
     * so a low `$tries` would fail jobs merely for being rate limited. Instead we
     * bound retries by time via retryUntil().
     */
    public int $maxExceptions = 3;

    /**
     * Máximo que o lock de unicidade fica retido (rede de segurança caso o job
     * morra sem liberar). Na prática o lock é solto assim que o job conclui.
     */
    public int $uniqueFor = 3600;

    /**
     * @param  bool  $rebaseline  Quando true, grava o snapshot mas NÃO gera
     *                            change_events nem notificação — usado uma vez ao
     *                            trocar a fonte de dados (ex.: BrasilAPI → base
     *                            local), para o novo baseline não disparar alertas
     *                            de mera diferença de formato.
     */
    public function __construct(public MonitoredCompany $company, public bool $rebaseline = false) {}

    /**
     * Deduplica o enfileiramento: enquanto já houver um refresh pendente ou em
     * execução para esta empresa, novos dispatches (ex.: cliques repetidos em
     * "Atualizar todas") são descartados em vez de empilhar jobs iguais.
     */
    public function uniqueId(): string
    {
        return (string) $this->company->id;
    }

    /**
     * Keep retrying (including rate-limit releases) for up to 12 hours, which is
     * plenty for a large batch to drain at the throttled rate.
     */
    public function retryUntil(): DateTimeInterface
    {
        return Carbon::now()->addHours(12);
    }

    /**
     * Never refresh the same company concurrently, and cap the global refresh
     * rate when the CNPJ provider is external (see the 'cnpj-refresh' limiter —
     * with the local base it is unlimited).
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping((string) $this->company->id))->releaseAfter(60)->expireAfter(120),
            new RateLimited('cnpj-refresh'),
        ];
    }

    public function handle(CnpjDataProvider $provider, SnapshotService $snapshots, CompanyDiffer $differ, DirtyListMonitor $dirtyList, CompanyAlertNotifier $notifier): void
    {
        // A cancelled batch (e.g. the monthly run was aborted) should stop here.
        if ($this->batch()?->cancelled()) {
            return;
        }

        $data = $provider->fetch($this->company->cnpj);

        // Company not found upstream — record the attempt, nothing to diff.
        if ($data === null) {
            $this->markRefreshed(RefreshStatus::NotFound);

            return;
        }

        // Re-baseline: grava o snapshot como novo ponto de partida, sem diff nem
        // alerta. Absorve a troca de fonte de dados (a 1ª coleta local não deve
        // alertar por diferença de formato — ver V2-F2/A3).
        if ($this->rebaseline) {
            DB::transaction(function () use ($data, $snapshots): void {
                $snapshots->store($this->company, $data);
                $this->markRefreshed(RefreshStatus::Ok);
            });

            // Baseline silencioso da Lista Suja junto do rebaseline. (Sanções têm
            // baseline próprio na 1ª passada da varredura diária.)
            $dirtyList->sync($this->company, silent: true);

            return;
        }

        // Capture the baseline (with its partners) via a fresh query before
        // writing the new snapshot — never a possibly-stale relation cache.
        $previous = $this->company->snapshots()
            ->with('partners')
            ->orderByDesc('captured_at')
            ->orderByDesc('id')
            ->first();

        // Snapshot + change_events + status num único átomo: se algo falhar no
        // meio, o retry recomeça limpo (sem snapshot órfão nem eventos parciais).
        $snapshot = null;
        $events = DB::transaction(function () use ($previous, $data, $snapshots, $differ, &$snapshot): EloquentCollection {
            $snapshot = $snapshots->store($this->company, $data);
            $changes = $this->persistChanges($previous, $snapshot, $differ->diff($previous, $snapshot));
            $this->markRefreshed(RefreshStatus::Ok);

            return $changes;
        });

        // Entrou na Lista Suja (trabalho escravo) desde o último refresh → alerta
        // crítico. Consulta local (tabela pequena), barata o bastante para o refresh.
        if ($snapshot !== null) {
            $listaSuja = $dirtyList->sync($this->company);

            if ($listaSuja !== null) {
                $events->push($this->persistDirtyListEvent($snapshot, $listaSuja));
            }
        }

        $notifier->notify($this->company, $data->razaoSocial, $events);
    }

    /**
     * Record the outcome once the job ultimately fails (after maxExceptions),
     * so the error is visible per-company in the UI.
     */
    public function failed(?Throwable $e): void
    {
        MonitoredCompany::whereKey($this->company->id)->update([
            'last_refreshed_at' => Carbon::now(),
            'last_refresh_status' => RefreshStatus::Error->value,
            'last_refresh_error' => $e === null ? 'Falha desconhecida' : mb_substr($e->getMessage(), 0, 480),
        ]);
    }

    /**
     * Persist the diffed changes as change_events rows.
     *
     * @param  list<ChangeEventData>  $changes
     * @return EloquentCollection<int, ChangeEvent>
     */
    private function persistChanges(?CompanySnapshot $previous, CompanySnapshot $snapshot, array $changes): EloquentCollection
    {
        $now = Carbon::now();

        /** @var EloquentCollection<int, ChangeEvent> $events */
        $events = new EloquentCollection;

        foreach ($changes as $change) {
            $events->push($this->company->changeEvents()->create([
                'from_snapshot_id' => $previous?->id,
                'to_snapshot_id' => $snapshot->id,
                'type' => $change->type,
                'field' => $change->field,
                'old_value' => $change->oldValue,
                'new_value' => $change->newValue,
                'severity' => $change->severity,
                'detected_at' => $now,
            ]));
        }

        return $events;
    }

    /**
     * Entrada nova na Lista Suja do trabalho escravo → change_event crítico
     * (ligado ao snapshot atual, pois `to_snapshot_id` é obrigatório).
     */
    private function persistDirtyListEvent(CompanySnapshot $snapshot, DirtyListEmployer $entry): ChangeEvent
    {
        $detalhe = collect([$entry->nome, $entry->uf, $entry->ano])->filter()->implode(' · ');

        return $this->company->changeEvents()->create([
            'from_snapshot_id' => null,
            'to_snapshot_id' => $snapshot->id,
            'type' => ChangeType::ListaSuja,
            'field' => 'lista_suja',
            'old_value' => null,
            'new_value' => $detalhe !== '' ? $detalhe : 'Consta na Lista Suja (MTE)',
            'severity' => Severity::Critical,
            'detected_at' => Carbon::now(),
        ]);
    }

    private function markRefreshed(RefreshStatus $status): void
    {
        $this->company->forceFill([
            'last_refreshed_at' => Carbon::now(),
            'last_refresh_status' => $status,
            'last_refresh_error' => null,
        ])->save();
    }
}
