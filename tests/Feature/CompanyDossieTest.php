<?php

declare(strict_types=1);

use App\Models\CompanyPartner;
use App\Models\CompanySnapshot;
use App\Models\DirtyListEmployer;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

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

it('renders the rich risk sections in the PDF (sanction, dirty list and partners)', function () {
    // Exercita as ramificações "caras" do Blade do PDF (box de risco avaliado,
    // lista de sanções, seção Lista Suja, QSA) — um erro de Blade ali derruba o
    // render do dompdf e falha o teste.
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(function (Request $request) {
        if (str_contains($request->url(), '/ceis')) {
            return Http::response([[
                'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
                'tipoSancao' => ['descricaoResumida' => 'Empresa inidônea'],
                'orgaoSancionador' => ['nome' => 'TCU'],
                'dataInicioSancao' => '01/03/2024',
            ]]);
        }

        return Http::response([]);
    });

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);
    $snapshot = CompanySnapshot::factory()->create([
        'monitored_company_id' => $company->id,
        'razao_social' => 'MATRIZ INDUSTRIAL LTDA',
    ]);
    CompanyPartner::factory()->create([
        'company_snapshot_id' => $snapshot->id,
        'nome' => 'FULANO DE TAL',
        'qualificacao' => 'Sócio-Administrador',
    ]);
    DirtyListEmployer::query()->create([
        'documento' => '11222333000181',
        'nome' => 'MATRIZ INDUSTRIAL LTDA',
        'uf' => 'PA',
        'ano' => '2022',
        'imported_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('companies.dossie', $company))
        ->assertOk()
        ->assertDownload('dossie-11222333000181.pdf');
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
