<?php

declare(strict_types=1);

use App\Enums\Plan;
use App\Livewire\Admin\Organizations\Show as OrgShow;
use App\Livewire\Portfolios\Show as PortfolioShow;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

it('reads plan limits and labels', function () {
    config()->set('plans.pro.max_companies', 200);

    expect(Plan::Pro->maxCompanies())->toBe(200)
        ->and(Plan::Free->label())->toBe('Free')
        ->and(Plan::Business->label())->toBe('Business');
});

it('counts monitored companies org-wide and computes remaining slots', function () {
    config()->set('plans.free.max_companies', 5);

    $org = Organization::factory()->create(); // plano free (default)
    $p1 = Portfolio::factory()->for($org)->create();
    $p2 = Portfolio::factory()->for($org)->create();
    MonitoredCompany::factory()->count(2)->for($p1)->create();
    MonitoredCompany::factory()->count(1)->for($p2)->create();

    expect($org->monitoredCompaniesCount())->toBe(3)
        ->and($org->maxMonitoredCompanies())->toBe(5)
        ->and($org->remainingCompanySlots())->toBe(2);
});

it('blocks adding a company when the plan quota is reached', function () {
    config()->set('plans.free.max_companies', 2);

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    MonitoredCompany::factory()->count(2)->for($portfolio)->create();

    Livewire::actingAs($user)->test(PortfolioShow::class, ['portfolio' => $portfolio])
        ->set('cnpj', '11.222.333/0001-81')
        ->call('addCompany')
        ->assertHasErrors('cnpj');

    expect($portfolio->monitoredCompanies()->count())->toBe(2);
});

it('allows adding a company while under the plan quota', function () {
    config()->set('plans.free.max_companies', 5);

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    Livewire::actingAs($user)->test(PortfolioShow::class, ['portfolio' => $portfolio])
        ->set('cnpj', '11.222.333/0001-81')
        ->call('addCompany')
        ->assertHasNoErrors();

    expect($portfolio->monitoredCompanies()->count())->toBe(1);
});

it('caps a CSV import at the remaining plan slots', function () {
    config()->set('plans.free.max_companies', 2);

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '00000000000191']); // 1 usado → resta 1

    $file = UploadedFile::fake()->createWithContent(
        'companies.csv',
        "cnpj\n11.222.333/0001-81\n33.000.167/0001-01\n60.701.190/0001-04\n",
    );

    Livewire::actingAs($user)->test(PortfolioShow::class, ['portfolio' => $portfolio])
        ->set('csv', $file)
        ->call('import')
        ->assertHasNoErrors();

    // Só 1 vaga: importa 1 e para no teto (as outras 2 rejeitadas por limite).
    expect($portfolio->monitoredCompanies()->count())->toBe(2)
        ->and($org->fresh()->monitoredCompaniesCount())->toBe(2);
});

it('lets the super-admin change an organization plan and raise the quota', function () {
    config()->set('plans.business.max_companies', 2000);

    $super = User::factory()->superAdmin()->create();
    $org = Organization::factory()->create(); // free

    Livewire::actingAs($super)->test(OrgShow::class, ['organization' => $org])
        ->call('setPlan', 'business');

    expect($org->fresh()->plan)->toBe(Plan::Business)
        ->and($org->fresh()->maxMonitoredCompanies())->toBe(2000);
});

it('forbids a non super-admin from changing an organization plan', function () {
    $super = User::factory()->superAdmin()->create();
    $org = Organization::factory()->create();

    $component = Livewire::actingAs($super)->test(OrgShow::class, ['organization' => $org]);

    // Privilégio perdido após o mount (o /livewire/update não re-roda mount).
    $super->forceFill(['is_super_admin' => false])->save();

    $component->call('setPlan', 'business')->assertForbidden();

    expect($org->fresh()->plan)->toBe(Plan::Free); // inalterado
});
