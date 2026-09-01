<?php

declare(strict_types=1);

use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;

it('renders the company detail for its owner', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create();

    $this->actingAs($user)
        ->get(route('companies.show', $company))
        ->assertOk()
        ->assertSee($company->formattedCnpj());
});

it('keeps the company label visible next to the official name on the detail page', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create([
        'label' => 'UNIDADE-77',
    ]);
    CompanySnapshot::factory()->create([
        'monitored_company_id' => $company->id,
        'razao_social' => 'MATRIZ INDUSTRIAL LTDA',
    ]);

    $this->actingAs($user)
        ->get(route('companies.show', $company))
        ->assertOk()
        ->assertSee('MATRIZ INDUSTRIAL LTDA')
        ->assertSee('UNIDADE-77'); // o código do sistema segue visível após os dados chegarem
});

it('forbids viewing a company from another organization', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $foreign = MonitoredCompany::factory()->create(); // its own (different) org

    $this->actingAs($user)
        ->get(route('companies.show', $foreign))
        ->assertForbidden();
});

it('returns 404 for a portfolio from another organization (global scope)', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $foreign = Portfolio::factory()->create();

    $this->actingAs($user)
        ->get(route('portfolios.show', $foreign))
        ->assertNotFound();
});

it('redirects guests to login', function () {
    $company = MonitoredCompany::factory()->create();

    $this->get(route('companies.show', $company))->assertRedirect(route('login'));
});
