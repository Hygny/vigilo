<?php

declare(strict_types=1);

use App\Models\DirtyListEmployer;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Services\Risco\DirtyListMonitor;

function dlCompany(string $cnpj = '11222333000181'): MonitoredCompany
{
    $org = Organization::factory()->create();

    return MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => $cnpj]);
}

it('flags and returns the entry when the company newly enters the list', function () {
    DirtyListEmployer::query()->create(['documento' => '11222333000181', 'nome' => 'EMPRESA RUIM', 'uf' => 'PA', 'ano' => '2024', 'imported_at' => now()]);
    $company = dlCompany();

    $entry = app(DirtyListMonitor::class)->sync($company);

    expect($entry)->not->toBeNull()
        ->and($entry->nome)->toBe('EMPRESA RUIM')
        ->and($company->fresh()->dirty_list_flagged_at)->not->toBeNull();
});

it('does not re-alert when already flagged', function () {
    DirtyListEmployer::query()->create(['documento' => '11222333000181', 'imported_at' => now()]);
    $company = dlCompany();
    $monitor = app(DirtyListMonitor::class);

    expect($monitor->sync($company))->not->toBeNull(); // 1ª vez: alerta
    expect($monitor->sync($company))->toBeNull();        // 2ª vez: já conhecido
});

it('clears the flag when the company leaves the list', function () {
    $company = dlCompany();
    $company->forceFill(['dirty_list_flagged_at' => now()])->save(); // estava listada
    // Lista vazia → não consta mais.

    expect(app(DirtyListMonitor::class)->sync($company))->toBeNull();
    expect($company->fresh()->dirty_list_flagged_at)->toBeNull();
});

it('baselines silently (rebaseline): flags but does not alert', function () {
    DirtyListEmployer::query()->create(['documento' => '11222333000181', 'imported_at' => now()]);
    $company = dlCompany();

    expect(app(DirtyListMonitor::class)->sync($company, silent: true))->toBeNull();
    expect($company->fresh()->dirty_list_flagged_at)->not->toBeNull();
});
