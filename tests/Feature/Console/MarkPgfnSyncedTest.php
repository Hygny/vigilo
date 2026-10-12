<?php

declare(strict_types=1);

use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;

it('marks every monitored company as synced with the PGFN', function () {
    $org = Organization::factory()->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '11222333000181']);
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '44555666000199']);

    expect(MonitoredCompany::query()->whereNull('pgfn_synced_at')->count())->toBe(2);

    $this->artisan('vigilo:pgfn-marcar-sincronizado')->assertSuccessful();

    expect(MonitoredCompany::query()->whereNull('pgfn_synced_at')->count())->toBe(0);
});
