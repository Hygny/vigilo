<?php

declare(strict_types=1);

use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;

it('prints the monitored CNPJs as digits, one per line, deduped', function () {
    $org = Organization::factory()->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '11.222.333/0001-81']); // formatado
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '44555666000199']);

    $this->artisan('vigilo:carteira-cnpjs')
        ->expectsOutput('11222333000181') // sai só os dígitos
        ->expectsOutput('44555666000199')
        ->assertSuccessful();
});

it('ignores non-CNPJ (CPF-length) documents', function () {
    $org = Organization::factory()->create();
    MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '12345678901']); // 11 díg

    $this->artisan('vigilo:carteira-cnpjs')
        ->doesntExpectOutput('12345678901')
        ->assertSuccessful();
});
