<?php

declare(strict_types=1);

namespace App\Livewire\Alerts;

use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Models\ChangeEvent;
use App\Models\MonitoredCompany;
use App\Models\Portfolio;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Inbox extends Component
{
    use AuthorizesRequests, InteractsWithCurrentOrganization;

    /** Severity filter: 'all' or a Severity value. */
    #[Url]
    public string $severity = 'all';

    public function setSeverity(string $severity): void
    {
        $this->severity = $severity;
    }

    public function acknowledge(int $id): void
    {
        $event = $this->scopedEvents()->findOrFail($id);
        $this->authorize('view', $event->monitoredCompany);

        $event->update(['acknowledged_at' => Carbon::now()]);
        session()->flash('status', 'Alerta marcado como visto.');
    }

    public function acknowledgeAll(): void
    {
        // Respeita o filtro de severidade ativo: só reconhece o que está à vista.
        $this->openFilteredEvents()->update(['acknowledged_at' => Carbon::now()]);

        session()->flash('status', 'Alertas do filtro atual marcados como vistos.');
    }

    public function render(): View
    {
        $events = $this->openFilteredEvents()
            ->with('monitoredCompany')
            ->orderByDesc('detected_at')
            ->limit(100)
            ->get();

        return view('livewire.alerts.inbox', ['events' => $events]);
    }

    /**
     * Alertas em aberto (não reconhecidos) do filtro de severidade atual.
     *
     * @return Builder<ChangeEvent>
     */
    private function openFilteredEvents(): Builder
    {
        $query = $this->scopedEvents()->whereNull('acknowledged_at');

        if (in_array($this->severity, ['critical', 'high', 'medium', 'low'], true)) {
            $query->where('severity', $this->severity);
        }

        return $query;
    }

    /**
     * Change events belonging to the current organization only.
     *
     * @return Builder<ChangeEvent>
     */
    private function scopedEvents(): Builder
    {
        $this->currentOrganizationId();

        $companyIds = MonitoredCompany::query()
            ->whereIn('portfolio_id', Portfolio::query()->pluck('id'))
            ->pluck('id');

        return ChangeEvent::query()->whereIn('monitored_company_id', $companyIds);
    }
}
