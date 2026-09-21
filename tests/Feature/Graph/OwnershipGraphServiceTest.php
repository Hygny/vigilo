<?php

declare(strict_types=1);

use App\Services\Graph\OwnershipGraphService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function bootGraphBase(): void
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
}

it('builds a Camada 1 graph: center, partners and the economic group (reverse), deduped', function () {
    bootGraphBase();

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA B'],
    ]);
    DB::connection('cnpj')->table('qualificacoes_socios')->insert([['codigo' => '49', 'descricao' => 'Sócio-Administrador']]);
    DB::connection('cnpj')->table('socios')->insert([
        // sócios diretos da empresa A
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2', 'qualificacao_do_socio' => '49'],
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING X', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1', 'qualificacao_do_socio' => '49'],
        // MARIA também é sócia da empresa B (grupo econômico via aresta reversa)
        ['cnpj_basico' => '44555666', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2', 'qualificacao_do_socio' => '49'],
    ]);

    $graph = (new OwnershipGraphService('cnpj', 25))->for('11222333000181')->toArray();

    expect($graph['centro'])->toBe('empresa:11222333')
        ->and($graph['nos'])->toHaveCount(4); // A, MARIA, HOLDING X, B

    $byId = collect($graph['nos'])->keyBy('id');
    expect($byId->get('empresa:11222333'))->toMatchArray(['tipo' => 'empresa', 'nome' => 'EMPRESA A', 'situacao' => 'ATIVA'])
        ->and($byId->get('socio:***111**'))->toMatchArray(['tipo' => 'socio_pf', 'nome' => 'MARIA'])
        ->and($byId->get('socio:99888777000166'))->toMatchArray(['tipo' => 'socio_pj', 'nome' => 'HOLDING X'])
        ->and($byId->get('empresa:44555666'))->toMatchArray(['tipo' => 'empresa', 'nome' => 'EMPRESA B']);

    $edges = collect($graph['arestas']);
    // direta: A → MARIA (com qualificação); reversa: B → MARIA (sócio em comum)
    expect($edges->contains(fn (array $e): bool => $e['de'] === 'empresa:11222333' && $e['para'] === 'socio:***111**' && $e['qualificacao'] === 'Sócio-Administrador'))->toBeTrue()
        ->and($edges->contains(fn (array $e): bool => $e['de'] === 'empresa:44555666' && $e['para'] === 'socio:***111**'))->toBeTrue();
});

it('caps the reverse expansion per partner', function () {
    bootGraphBase();

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '00000001', 'razao_social' => 'B1'],
        ['cnpj_basico' => '00000002', 'razao_social' => 'B2'],
        ['cnpj_basico' => '00000003', 'razao_social' => 'B3'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '00000001', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '00000002', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '00000003', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    // limite reverso = 2 → só 2 das 3 empresas de MARIA entram
    $graph = (new OwnershipGraphService('cnpj', 2))->for('11222333000181')->toArray();

    $empresasReversas = collect($graph['nos'])
        ->filter(fn (array $n): bool => $n['tipo'] === 'empresa' && $n['id'] !== 'empresa:11222333')
        ->count();

    expect($empresasReversas)->toBe(2);
});

it('degrades to a lone center node when the CNPJ is absent from the base', function () {
    bootGraphBase(); // tabelas vazias

    $graph = (new OwnershipGraphService('cnpj', 25))->for('11222333000181')->toArray();

    expect($graph['nos'])->toHaveCount(1)
        ->and($graph['arestas'])->toHaveCount(0)
        ->and($graph['centro'])->toBe('empresa:11222333');

    // sem linha na base → rótulo cai para o CNPJ e situação fica nula
    expect(collect($graph['nos'])->keyBy('id')->get('empresa:11222333'))
        ->toMatchArray(['tipo' => 'empresa', 'nome' => '11222333000181', 'situacao' => null]);
});

it('resolves the graph service from the container', function () {
    expect(app(OwnershipGraphService::class))->toBeInstanceOf(OwnershipGraphService::class);
});
