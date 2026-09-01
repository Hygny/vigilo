<?php

declare(strict_types=1);

use App\Livewire\Dashboard;
use App\Models\ChangeEvent;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use Livewire\Livewire;

it('shows counters scoped to the current organization', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for($portfolio)->create();
    ChangeEvent::factory()->for($company, 'monitoredCompany')->create(); // open + critical by default

    // Another organization's noise must not leak into the counters.
    $otherPortfolio = Portfolio::factory()->create();
    MonitoredCompany::factory()->count(4)->for($otherPortfolio)->create();

    Livewire::actingAs($user)->test(Dashboard::class)
        ->assertOk()
        ->assertViewHas('monitoredCount', 1)
        ->assertViewHas('portfoliosCount', 1)
        ->assertViewHas('openAlertsCount', 1)
        ->assertViewHas('openCriticalCount', 1);
});
