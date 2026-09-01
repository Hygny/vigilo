<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\Severity;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Models\ChangeEvent;
use App\Models\MonitoredCompany;
use App\Models\Portfolio;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    use InteractsWithCurrentOrganization;

    public function render(): View
    {
        $this->currentOrganizationId();

        $portfolioIds = Portfolio::query()->pluck('id');
        $companyIds = MonitoredCompany::query()->whereIn('portfolio_id', $portfolioIds)->pluck('id');

        $openAlerts = ChangeEvent::query()
            ->whereIn('monitored_company_id', $companyIds)
            ->whereNull('acknowledged_at');

        $recentChanges = ChangeEvent::query()
            ->whereIn('monitored_company_id', $companyIds)
            ->with('monitoredCompany')
            ->latest('detected_at')
            ->limit(8)
            ->get();

        return view('livewire.dashboard', [
            'monitoredCount' => $companyIds->count(),
            'portfoliosCount' => $portfolioIds->count(),
            'openAlertsCount' => (clone $openAlerts)->count(),
            'openCriticalCount' => (clone $openAlerts)->where('severity', Severity::Critical->value)->count(),
            'recentChanges' => $recentChanges,
        ]);
    }
}
