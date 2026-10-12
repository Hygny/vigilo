<?php

declare(strict_types=1);

use App\Models\CompanyPartner;
use App\Models\CompanySnapshot;
use App\Models\DirtyListEmployer;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\PgfnDebt;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

// Por padrão a base CNPJ (grafo) fica indisponível → o dossiê gera sem as seções
// do grafo (beneficiários/grupo/filiais), que degradam. O teste dedicado abaixo
// popula a base para exercitar essas seções.
beforeEach(function (): void {
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => false,
    ]);
    DB::purge('cnpj');
});

/** Base CNPJ (sqlite) com centro + sócio PJ (grupo) + sócio PF (beneficiário) + filial. */
function bootDossieCnpjBase(): void
{
    $schema = Schema::connection('cnpj');
    $schema->create('estabelecimentos', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('cnpj_ordem');
        $t->string('cnpj_dv');
        $t->string('situacao_cadastral')->nullable();
        $t->string('nome_fantasia')->nullable();
        $t->string('cep')->nullable();
        $t->string('numero')->nullable();
        $t->string('complemento')->nullable();
        $t->string('municipio')->nullable();
        $t->string('uf')->nullable();
    });
    $schema->create('empresas', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('razao_social')->nullable();
    });
    $schema->create('socios', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('nome_socio')->nullable();
        $t->string('cnpj_cpf_do_socio')->nullable();
        $t->string('identificador_de_socio')->nullable();
        $t->string('qualificacao_do_socio')->nullable();
    });
    $schema->create('qualificacoes_socios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    $schema->create('municipios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02', 'nome_fantasia' => null, 'uf' => null],
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0002', 'cnpj_dv' => '62', 'situacao_cadastral' => '08', 'nome_fantasia' => 'FILIAL RJ', 'uf' => 'RJ'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA CENTRO'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA GRUPO'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'HOLDING', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
    ]);
}

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

it('renders the rich risk sections in the PDF (sanction, dirty list, PGFN and partners)', function () {
    // Exercita as ramificações "caras" do Blade do PDF (box de risco avaliado,
    // lista de sanções, seção Lista Suja, seção PGFN, QSA) — um erro de Blade
    // ali derruba o render do dompdf e falha o teste.
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
    PgfnDebt::query()->create([
        'documento' => '11222333000181',
        'tipo' => 'geral',
        'valor' => '12345.67',
        'imported_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('companies.dossie', $company))
        ->assertOk()
        ->assertDownload('dossie-11222333000181.pdf');
});

it('renders the graph sections (beneficiaries, economic group, branches) when the base is available', function () {
    bootDossieCnpjBase(); // centro + sócio PJ (grupo) + sócio PF + filial

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    // Gera o PDF exercitando as 3 ramificações novas do Blade (beneficiários,
    // grupo econômico, filiais). Um erro de Blade ali derrubaria o render.
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
