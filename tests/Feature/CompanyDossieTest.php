<?php

declare(strict_types=1);

use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;

it('downloads the due-diligence PDF for the company owner', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);
    CompanySnapshot::factory()->create([
        'monitored_company_id' => $company->id,
        'razao_social' => 'MATRIZ INDUSTRIAL LTDA',
    ]);

    $this->actingAs($user)
        ->get(route('companies.dossie', $company))
        ->assertOk()
        ->assertDownload('dossie-11222333000181.pdf');
});

it('generates the PDF even before any snapshot exists', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    $this->actingAs($user)
        ->get(route('companies.dossie', $company))
        ->assertOk();
});

it('redirects guests to login', function () {
    $company = MonitoredCompany::factory()->create(['cnpj' => '11222333000181']);

    $this->get(route('companies.dossie', $company))->assertRedirect(route('login'));
});

it('forbids downloading the dossier of a company from another organization', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $foreign = MonitoredCompany::factory()->create();

    $this->actingAs($user)->get(route('companies.dossie', $foreign))->assertForbidden();
});
