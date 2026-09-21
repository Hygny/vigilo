<?php

declare(strict_types=1);

use App\Livewire\Companies\Graph;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Support\Cnpj;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

/**
 * Aponta a conexão `cnpj` para sqlite. Com $withData, cria e popula as tabelas
 * (centro + sócia + empresa do grupo via aresta reversa); sem, deixa as tabelas
 * ausentes para forçar o caminho "grafo indisponível".
 */
function bootCompanyGraphBase(bool $withData = true): void
{
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);

    DB::purge('cnpj');

    if (! $withData) {
        return;
    }

    $schema = Schema::connection('cnpj');
    $schema->create('estabelecimentos', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('cnpj_ordem');
        $t->string('cnpj_dv');
        $t->string('situacao_cadastral')->nullable();
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

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02',
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA CENTRO'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA GRUPO'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);
}

function monitoredCompanyFor(Organization $org, string $cnpj = '11222333000181'): MonitoredCompany
{
    return MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => $cnpj]);
}

it('renders the ownership graph for a monitored company', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    $grupoCnpj = Cnpj::matrizFromBasico('44555666');

    $this->actingAs($user)->get(route('companies.graph', $company))
        ->assertOk()
        ->assertSee('Grafo societário')
        ->assertSee('EMPRESA CENTRO')          // nó centro
        ->assertSee('MARIA')                   // sócia direta
        ->assertSee('EMPRESA GRUPO')           // grupo econômico (aresta reversa)
        ->assertSee('Beneficiários finais')    // painel de beneficiário final
        ->assertSee('wire:click="focus(\''.$grupoCnpj, false) // empresa do grupo é clicável
        ->assertDontSee("focus('***", false);  // PF (CPF mascarado) NÃO é clicável
});

it('shows an unavailable notice when the CNPJ base is down', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase(withData: false); // tabelas ausentes → serviço falha

    $this->actingAs($user)->get(route('companies.graph', $company))
        ->assertOk()
        ->assertSee('Grafo indisponível');
});

it('forbids viewing the graph of a company from another organization', function () {
    $owner = Organization::factory()->create();
    $company = monitoredCompanyFor($owner);
    bootCompanyGraphBase();

    $intruder = User::factory()->for(Organization::factory())->create();

    $this->actingAs($intruder)->get(route('companies.graph', $company))->assertForbidden();
});

it('recenters the graph when focusing another company, and resets back', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    // Dá conteúdo próprio à empresa do grupo (44555666), para o foco nela render.
    $grupoCnpj = Cnpj::matrizFromBasico('44555666');
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => substr((string) $grupoCnpj, 12, 2), 'situacao_cadastral' => '02',
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '44555666', 'nome_socio' => 'PEDRO', 'cnpj_cpf_do_socio' => '***777**', 'identificador_de_socio' => '2'],
    ]);

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->assertSee('EMPRESA CENTRO')
        ->call('focus', $grupoCnpj)
        ->assertSee('EMPRESA GRUPO')          // novo centro
        ->assertSee('PEDRO')                  // sócio do novo centro
        ->assertSee('Voltar à empresa monitorada')
        ->call('resetFocus')
        ->assertSee('EMPRESA CENTRO');        // voltou ao início
});

it('ignores a focus with an invalid CNPJ', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->call('focus', '123')
        ->assertSee('EMPRESA CENTRO'); // permanece na empresa monitorada
});
