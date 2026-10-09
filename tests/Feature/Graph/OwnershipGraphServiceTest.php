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
        $t->string('nome_fantasia')->nullable();
        $t->string('cep')->nullable();
        $t->string('numero')->nullable();
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
}

it('builds a Camada 1 graph: direct partners + the PJ economic group (reverse), by default', function () {
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
        // sócios diretos de A: MARIA (PF) e a HOLDING X (PJ)
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2', 'qualificacao_do_socio' => '49'],
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING X', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1', 'qualificacao_do_socio' => '49'],
        // a HOLDING X (PJ, CNPJ completo) também é sócia de B → grupo via reversa CERTA
        ['cnpj_basico' => '44555666', 'nome_socio' => 'HOLDING X', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1', 'qualificacao_do_socio' => '49'],
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
    // direta: A → MARIA (com qualificação); reversa CERTA: B → HOLDING X (PJ em comum), sólida
    expect($edges->contains(fn (array $e): bool => $e['de'] === 'empresa:11222333' && $e['para'] === 'socio:***111**' && $e['qualificacao'] === 'Sócio-Administrador'))->toBeTrue()
        ->and($edges->first(fn (array $e): bool => $e['de'] === 'empresa:44555666' && $e['para'] === 'socio:99888777000166')['provavel'])->toBeFalse();
});

it('hides the PF economic group by default and shows it (as probable) with the toggle', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA B'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA SILVA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'MARIA SILVA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    $service = new OwnershipGraphService('cnpj', 25);

    // Padrão: sócio PF não traz grupo reverso → B fica de fora.
    expect(collect($service->for('11222333000181')->toArray()['nos'])->pluck('id'))
        ->not->toContain('empresa:44555666');

    // Com includePfGroup: B entra, marcada como provável (menor certeza).
    $edges = collect($service->for('11222333000181', includePfGroup: true)->toArray()['arestas']);
    expect($edges->firstWhere('de', 'empresa:44555666')['provavel'])->toBeTrue();
});

it('filters the PF reverse by first name: a different first name (xará) is excluded', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA CERTA'],
        ['cnpj_basico' => '77888999', 'razao_social' => 'EMPRESA XARA'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA SILVA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        // mesmo nome → entra
        ['cnpj_basico' => '44555666', 'nome_socio' => 'MARIA SILVA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        // mesmo CPF mascarado, PRIMEIRO nome diferente → filtrado já na query
        ['cnpj_basico' => '77888999', 'nome_socio' => 'JOAO SOUZA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    $ids = collect((new OwnershipGraphService('cnpj', 25))->for('11222333000181', includePfGroup: true)->toArray()['nos'])->pluck('id');

    expect($ids)->toContain('empresa:44555666')
        ->and($ids)->not->toContain('empresa:77888999');
});

it('excludes a PF match with the same first name but a different surname', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA B'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA SILVA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        // 1º nome igual (passa no SQL) mas sobrenome diferente → cortado no PHP
        ['cnpj_basico' => '44555666', 'nome_socio' => 'MARIA SOUZA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    $ids = collect((new OwnershipGraphService('cnpj', 25))->for('11222333000181', includePfGroup: true)->toArray()['nos'])->pluck('id');

    expect($ids)->not->toContain('empresa:44555666');
});

it('keeps a PF match for the same person with a dropped middle name', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA B'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA APARECIDA SILVA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        // nome do meio omitido: 1º (MARIA) casa no SQL, 1º+último (MARIA/SILVA) casam → entra
        ['cnpj_basico' => '44555666', 'nome_socio' => 'MARIA SILVA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    $ids = collect((new OwnershipGraphService('cnpj', 25))->for('11222333000181', includePfGroup: true)->toArray()['nos'])->pluck('id');

    expect($ids)->toContain('empresa:44555666');
});

it('always includes the PJ reverse group (solid, not probable), even by default', function () {
    bootGraphBase();

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA B'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING X', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'HOLDING X SA', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
    ]);

    // PJ aparece no padrão (sem toggle) e a aresta é sólida (provavel=false).
    $edges = collect((new OwnershipGraphService('cnpj', 25))->for('11222333000181')->toArray()['arestas']);

    expect($edges->firstWhere('de', 'empresa:44555666')['provavel'])->toBeFalse();
});

it('carries the situação of the PJ reverse-group companies (negative shows off-center)', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
        // a matriz da empresa do grupo (via sócio PJ) está BAIXADA (08)
        ['cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => '55', 'situacao_cadastral' => '08'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA B'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING X', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'HOLDING X', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
    ]);

    $graph = (new OwnershipGraphService('cnpj', 25))->for('11222333000181')->toArray();

    expect(collect($graph['nos'])->firstWhere('id', 'empresa:44555666')['situacao'])->toBe('BAIXADA');
});

it('lists the filiais (sibling establishments) with situação and city, excluding the center', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02', 'nome_fantasia' => 'MATRIZ SP', 'municipio' => '7107', 'uf' => 'SP'],
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0002', 'cnpj_dv' => '62', 'situacao_cadastral' => '08', 'nome_fantasia' => 'FILIAL RJ', 'municipio' => '6001', 'uf' => 'RJ'],
    ]);
    DB::connection('cnpj')->table('municipios')->insert([
        ['codigo' => '7107', 'descricao' => 'SAO PAULO'],
        ['codigo' => '6001', 'descricao' => 'RIO DE JANEIRO'],
    ]);

    $branches = (new OwnershipGraphService('cnpj', 25))->branches('11222333000181');

    // Só a filial 0002 — o centro (0001) é excluído.
    expect($branches)->toHaveCount(1);
    expect($branches[0]->cnpj)->toBe('11222333000262')
        ->and($branches[0]->isMatriz)->toBeFalse()
        ->and($branches[0]->situacao)->toBe('BAIXADA')
        ->and($branches[0]->municipio)->toBe('RIO DE JANEIRO')
        ->and($branches[0]->uf)->toBe('RJ');
});

it('returns no filiais when the company has a single establishment', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
    ]);

    expect((new OwnershipGraphService('cnpj', 25))->branches('11222333000181'))->toBe([]);
});

it('marks the matriz with isMatriz=true when centering on a filial', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02', 'nome_fantasia' => 'MATRIZ', 'municipio' => null, 'uf' => 'SP'],
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0002', 'cnpj_dv' => '62', 'situacao_cadastral' => '02', 'nome_fantasia' => 'FILIAL', 'municipio' => null, 'uf' => 'RJ'],
    ]);

    // Centrado na FILIAL (ordem 0002): a matriz (0001) volta marcada como matriz.
    $branches = (new OwnershipGraphService('cnpj', 25))->branches('11222333000262');

    expect($branches)->toHaveCount(1);
    expect($branches[0]->cnpj)->toBe('11222333000181')
        ->and($branches[0]->isMatriz)->toBeTrue();
});

it('carries the situação of a person-centered graph companies', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => '55', 'situacao_cadastral' => '08'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA B'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '44555666', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    $graph = (new OwnershipGraphService('cnpj', 25))->forPerson('***111**')->toArray();

    expect(collect($graph['nos'])->firstWhere('id', 'empresa:44555666')['situacao'])->toBe('BAIXADA');
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

    // limite reverso = 2 → só 2 das 3 empresas de MARIA entram (grupo PF opt-in)
    $graph = (new OwnershipGraphService('cnpj', 2))->for('11222333000181', includePfGroup: true)->toArray();

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

// ---------------------------------------------------------------------------
// Grafo centrado na PESSOA (forPerson)
// ---------------------------------------------------------------------------

it('builds a person-centered graph: the person and their companies, deduped, foreigner typed', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA B'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        // JOHN (estrangeiro) aparece 2x na mesma empresa (dedup) + em outra
        ['cnpj_basico' => '11222333', 'nome_socio' => 'JOHN', 'cnpj_cpf_do_socio' => '***999**', 'identificador_de_socio' => '3'],
        ['cnpj_basico' => '11222333', 'nome_socio' => 'JOHN', 'cnpj_cpf_do_socio' => '***999**', 'identificador_de_socio' => '3'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'JOHN', 'cnpj_cpf_do_socio' => '***999**', 'identificador_de_socio' => '3'],
    ]);

    $graph = (new OwnershipGraphService('cnpj', 25))->forPerson('***999**')->toArray();

    expect($graph['centro'])->toBe('socio:***999**');

    $byId = collect($graph['nos'])->keyBy('id');
    expect($byId->get('socio:***999**'))->toMatchArray(['tipo' => 'socio_ext', 'nome' => 'JOHN']);

    // 11222333 deduplicada + 44555666 = 2 empresas
    expect(collect($graph['nos'])->where('tipo', 'empresa')->count())->toBe(2);
});

it('refines a person-centered graph by name when the masked CPF collides', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA B'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        // mesmo CPF mascarado, pessoas diferentes (nomes distintos)
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA SILVA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'JOAO SOUZA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    $graph = (new OwnershipGraphService('cnpj', 25))->forPerson('***111**', 'MARIA SILVA')->toArray();

    $empresas = collect($graph['nos'])->where('tipo', 'empresa')->pluck('nome');
    expect($empresas)->toContain('EMPRESA A')
        ->and($empresas)->not->toContain('EMPRESA B'); // não mistura com o JOAO
});

it('caps the number of companies in a person-centered graph', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '00000001', 'razao_social' => 'C1'],
        ['cnpj_basico' => '00000002', 'razao_social' => 'C2'],
        ['cnpj_basico' => '00000003', 'razao_social' => 'C3'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '00000001', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '00000002', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '00000003', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    $graph = (new OwnershipGraphService('cnpj', 2))->forPerson('***111**')->toArray();

    expect(collect($graph['nos'])->where('tipo', 'empresa')->count())->toBe(2); // cap 2 de 3
});

// ---------------------------------------------------------------------------
// Vizinhos de endereço (includeAddress)
// ---------------------------------------------------------------------------

it('includes other companies at the same address (cep + numero) with includeAddress', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => '100'],
        ['cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => '07', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => 'Nº 100'], // mesmo prédio (número normaliza)
        ['cnpj_basico' => '77888999', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => '500'], // número diferente
        ['cnpj_basico' => '12121212', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02', 'cep' => '80000000', 'numero' => '100'], // CEP diferente
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'CENTRO'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'VIZINHO'],
        ['cnpj_basico' => '77888999', 'razao_social' => 'OUTRO NUMERO'],
        ['cnpj_basico' => '12121212', 'razao_social' => 'OUTRO CEP'],
    ]);

    $graph = (new OwnershipGraphService('cnpj', 25))->for('11222333000181', includeAddress: true)->toArray();

    $ids = collect($graph['nos'])->pluck('id');
    expect($ids)->toContain('empresa:44555666')
        ->and($ids)->not->toContain('empresa:77888999')
        ->and($ids)->not->toContain('empresa:12121212');

    expect(collect($graph['arestas'])->where('tipo', 'endereco'))->toHaveCount(1);
});

it('omits address neighbors by default', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => '100'],
        ['cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => '07', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => '100'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'CENTRO'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'VIZINHO'],
    ]);

    $ids = collect((new OwnershipGraphService('cnpj', 25))->for('11222333000181')->toArray()['nos'])->pluck('id');
    expect($ids)->not->toContain('empresa:44555666');
});

it('caps the number of address neighbors at addressLimit', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => '100'],
        ['cnpj_basico' => '22222222', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => '100'],
        ['cnpj_basico' => '33333333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => '100'],
    ]);

    // addressLimit = 1 → só um dos dois vizinhos entra.
    $graph = (new OwnershipGraphService('cnpj', 25, addressLimit: 1))->for('11222333000181', includeAddress: true)->toArray();

    expect(collect($graph['arestas'])->where('tipo', 'endereco'))->toHaveCount(1);
});

it('adds no address neighbors when the center CEP is all zeros', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02', 'cep' => '00000000', 'numero' => '100'],
        ['cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => '07', 'situacao_cadastral' => '02', 'cep' => '00000000', 'numero' => '100'],
    ]);

    $graph = (new OwnershipGraphService('cnpj', 25))->for('11222333000181', includeAddress: true)->toArray();

    expect(collect($graph['arestas'])->where('tipo', 'endereco'))->toHaveCount(0);
});

// ---------------------------------------------------------------------------
// Beneficiário final estrutural
// ---------------------------------------------------------------------------

it('lists a direct natural person as a beneficial owner at level 1', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    $owners = (new OwnershipGraphService('cnpj', 25))->beneficialOwners('11222333000181');

    expect($owners)->toHaveCount(1)
        ->and($owners[0]->name)->toBe('MARIA')
        ->and($owners[0]->type)->toBe('pf')
        ->and($owners[0]->depth)->toBe(1);
});

it('climbs through a PJ partner to the natural person at the top (level 2)', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('socios')->insert([
        // centro tem sócio PJ = holding 99888777
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING X', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
        // a holding tem sócio PF JOAO
        ['cnpj_basico' => '99888777', 'nome_socio' => 'JOAO', 'cnpj_cpf_do_socio' => '***999**', 'identificador_de_socio' => '2'],
    ]);

    $owners = (new OwnershipGraphService('cnpj', 25))->beneficialOwners('11222333000181');

    // A holding (PJ) não é beneficiário; JOAO no topo, nível 2.
    expect($owners)->toHaveCount(1)
        ->and($owners[0]->name)->toBe('JOAO')
        ->and($owners[0]->depth)->toBe(2);
});

it('respects the depth limit (does not climb PJ partners beyond it)', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING X', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '99888777', 'nome_socio' => 'JOAO', 'cnpj_cpf_do_socio' => '***999**', 'identificador_de_socio' => '2'],
    ]);

    // profundidade 1 → não sobe na holding → nenhum PF alcançado
    $owners = (new OwnershipGraphService('cnpj', 25))->beneficialOwners('11222333000181', maxDepth: 1);

    expect($owners)->toHaveCount(0);
});

it('is cycle-safe when two companies own each other', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'EMP B', 'cnpj_cpf_do_socio' => '44555666000155', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'EMP A', 'cnpj_cpf_do_socio' => '11222333000181', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'LUCIA', 'cnpj_cpf_do_socio' => '***222**', 'identificador_de_socio' => '2'],
    ]);

    $owners = (new OwnershipGraphService('cnpj', 25))->beneficialOwners('11222333000181');

    // Termina (visited impede loop) e chega em LUCIA.
    expect(collect($owners)->pluck('name')->all())->toContain('LUCIA');
});

it('dedups a person reached by multiple paths, keeping the nearest level', function () {
    bootGraphBase();
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '99888777', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    $owners = (new OwnershipGraphService('cnpj', 25))->beneficialOwners('11222333000181');

    expect(collect($owners)->where('document', '***111**'))->toHaveCount(1)
        ->and(collect($owners)->firstWhere('document', '***111**')->depth)->toBe(1);
});
