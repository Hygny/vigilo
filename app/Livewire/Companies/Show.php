<?php

declare(strict_types=1);

namespace App\Livewire\Companies;

use App\Jobs\RefreshMonitoredCompanyJob;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Models\MonitoredCompany;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    use AuthorizesRequests, InteractsWithCurrentOrganization;

    public MonitoredCompany $company;

    public function mount(MonitoredCompany $company): void
    {
        // Companies aren't globally scoped, so the policy is the tenant guard.
        $this->authorize('view', $company);
        $this->company = $company;
    }

    public function queueRefresh(): void
    {
        $this->authorize('update', $this->company);

        RefreshMonitoredCompanyJob::dispatch($this->company);
        session()->flash('status', 'Atualização enfileirada.');
    }

    public function render(): View
    {
        $latest = $this->company->snapshots()
            ->with('partners')
            ->orderByDesc('captured_at')
            ->orderByDesc('id')
            ->first();

        $snapshots = $this->company->snapshots()
            ->orderByDesc('captured_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $events = $this->company->changeEvents()
            ->orderByDesc('detected_at')
            ->limit(50)
            ->get();

        return view('livewire.companies.show', [
            'latest' => $latest,
            'snapshots' => $snapshots,
            'events' => $events,
        ]);
    }
}
