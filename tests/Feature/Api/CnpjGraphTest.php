<?php

declare(strict_types=1);

use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Base CNPJ local (sqlite em memória) com apenas a empresa-centro — suficiente
 * para o endpoint montar um grafo (o serviço tem teste próprio para as arestas).
 */
function seedGraphCenterBase(): void
{
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);

    DB::purge('cnpj');
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
    DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A']);
}

/**
 * @return array{0: Organization, 1: string}
 */
function orgWithToken(): array
{
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    return [$org, $user->createToken('test')->plainTextToken];
}

it('rejects an unauthenticated graph request', function () {
    $this->getJson('/api/cnpj/11222333000181/grafo')->assertUnauthorized();
});

it('returns 404 for a CNPJ not monitored by the organization', function () {
    [, $token] = orgWithToken();
    seedGraphCenterBase(); // existe na base, mas a org não monitora → escopo barra

    $this->withHeaders(['Authorization' => 'Bearer '.$token])
        ->getJson('/api/cnpj/11222333000181/grafo')
        ->assertNotFound();
});

it('returns the ownership graph for a monitored CNPJ', function () {
    [$org, $token] = orgWithToken();
    MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);
    seedGraphCenterBase();

    $this->withHeaders(['Authorization' => 'Bearer '.$token])
        ->getJson('/api/cnpj/11222333000181/grafo')
        ->assertOk()
        ->assertJsonPath('centro', 'empresa:11222333')
        ->assertJsonPath('nos.0.nome', 'EMPRESA A');
});

it('validates the CNPJ length', function () {
    [, $token] = orgWithToken();

    $this->withHeaders(['Authorization' => 'Bearer '.$token])
        ->getJson('/api/cnpj/123/grafo')
        ->assertStatus(422);
});
