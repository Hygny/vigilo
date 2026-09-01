<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\CnpjDataProvider;
use App\DTO\ChangeEventData;
use App\Enums\RefreshStatus;
use App\Models\ChangeEvent;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Notifications\CompanyChangeDetected;
use App\Services\CompanyDiffer;
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
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Refresh a single monitored company: fetch its current cadastral data, store a
 * new snapshot, diff it against the previous one, persist any change events and
 * notify the organization.
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

    public function __construct(public MonitoredCompany $company) {}

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
     * Guard the upstream provider: never refresh the same company concurrently,
     * and cap the global refresh rate (see the 'cnpj-refresh' limiter).
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

    public function handle(CnpjDataProvider $provider, SnapshotService $snapshots, CompanyDiffer $differ): void
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

        // Capture the baseline (with its partners) via a fresh query before
        // writing the new snapshot — never a possibly-stale relation cache.
        $previous = $this->company->snapshots()
            ->with('partners')
            ->orderByDesc('captured_at')
            ->orderByDesc('id')
            ->first();

        // Snapshot + change_events + status num único átomo: se algo falhar no
        // meio, o retry recomeça limpo (sem snapshot órfão nem eventos parciais).
        $events = DB::transaction(function () use ($previous, $data, $snapshots, $differ): EloquentCollection {
            $snapshot = $snapshots->store($this->company, $data);
            $changes = $this->persistChanges($previous, $snapshot, $differ->diff($previous, $snapshot));
            $this->markRefreshed(RefreshStatus::Ok);

            return $changes;
        });

        if ($events->isNotEmpty()) {
            $this->notifyOrganization($data->razaoSocial, $events);
        }
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
     * @param  EloquentCollection<int, ChangeEvent>  $events
     */
    private function notifyOrganization(string $razaoSocial, EloquentCollection $events): void
    {
        $users = $this->company->portfolio->organization->users;

        if ($users->isEmpty()) {
            return;
        }

        Notification::send(
            $users,
            new CompanyChangeDetected($this->company, $razaoSocial, $events)
        );
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
