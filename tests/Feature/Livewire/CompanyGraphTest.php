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

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => '100',
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA CENTRO'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA GRUPO'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        // MARIA (PF) liga centro+grupo mas só sob demanda; a HOLDING (PJ) liga
        // os dois de forma CERTA → o grupo aparece no padrão.
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'HOLDING', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
    ]);
}

function monitoredCompanyFor(Organization $org, string $cnpj = '11222333000181'): MonitoredCompany
{
    return MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => $cnpj]);
}

it('renders the ownership graph with clickable company and person data', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    $grupoCnpj = Cnpj::matrizFromBasico('44555666');

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->assertSee('Grafo societário')
        ->assertSee('Beneficiários finais')
        ->assertViewHas('cyto', function (array $cyto) use ($grupoCnpj): bool {
            $nodes = collect($cyto['nodes']);
            $centro = $nodes->firstWhere('data.label', 'EMPRESA CENTRO');
            $maria = $nodes->firstWhere('data.label', 'MARIA');
            $grupo = $nodes->firstWhere('data.label', 'EMPRESA GRUPO');

            expect($centro['data']['isCenter'])->toBeTrue()
                ->and($maria['data']['role'])->toBe('person')      // PF → foca a pessoa
                ->and($maria['data']['doc'])->toBe('***111**')
                ->and($maria['data']['cnpj'])->toBeNull()
                ->and($grupo['data']['role'])->toBe('company')     // empresa → foca a empresa
                ->and($grupo['data']['cnpj'])->toBe($grupoCnpj);

            return true;
        });
});

it('shows the filiais panel with the sibling establishments', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    // Filial da empresa monitorada (mesmo cnpj_basico, outra ordem), BAIXADA.
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0002', 'cnpj_dv' => '62', 'situacao_cadastral' => '08', 'nome_fantasia' => 'FILIAL RJ', 'municipio' => null, 'uf' => 'RJ'],
    ]);

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->assertSee('Filiais')
        ->assertSee('FILIAL RJ')
        ->assertSee('BAIXADA');
});

it('hides the PF economic group by default and reveals it (dashed) via the toggle, filtering xarás', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase(); // grupo 44555666 ligado por HOLDING (PJ) → sempre visível

    // Empresa ligada SÓ por MARIA (PF, mesmo nome) → oculta por padrão.
    DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => '55667788', 'razao_social' => 'EMPRESA PF']);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '55667788', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);
    // Xará: mesmo CPF mascarado, PRIMEIRO nome diferente → nunca aparece.
    DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => '77888999', 'razao_social' => 'EMPRESA XARA']);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '77888999', 'nome_socio' => 'JOAO SOUZA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        // Padrão: grupo PJ (44555666) visível; conexão só-PF (55667788) oculta.
        ->assertViewHas('cyto', function (array $cyto): bool {
            $edges = collect($cyto['edges']);
            expect($edges->firstWhere('data.source', 'empresa:44555666'))->not->toBeNull()   // PJ sempre
                ->and($edges->firstWhere('data.source', 'empresa:55667788'))->toBeNull();     // PF oculto

            return true;
        })
        // Liga as conexões por sócio PF: a 55667788 aparece (provável/tracejada);
        // o xará (77888999, outro 1º nome) continua filtrado.
        ->call('toggleProbable')
        ->assertViewHas('cyto', function (array $cyto): bool {
            $edges = collect($cyto['edges']);
            $pf = $edges->firstWhere('data.source', 'empresa:55667788');
            expect($pf)->not->toBeNull()
                ->and($pf['data']['probable'])->toBeTrue()
                ->and($edges->firstWhere('data.source', 'empresa:77888999'))->toBeNull();

            return true;
        });
});

it('hides same-address companies by default and reveals them via the toggle', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase(); // centro no CEP 49019900, nº 100

    // Empresa vizinha: mesmo CEP + número (empresa diferente, sem vínculo societário).
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '45456767', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => 'Nº 100'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => '45456767', 'razao_social' => 'VIZINHA DE ENDERECO']);

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->assertViewHas('cyto', fn (array $cyto): bool => collect($cyto['nodes'])->firstWhere('data.id', 'empresa:45456767') === null)
        ->call('toggleAddress')
        ->assertViewHas('cyto', function (array $cyto): bool {
            $vizinha = collect($cyto['nodes'])->firstWhere('data.id', 'empresa:45456767');
            $edge = collect($cyto['edges'])->firstWhere('data.type', 'endereco');
            expect($vizinha)->not->toBeNull()
                ->and($edge)->not->toBeNull()
                ->and($edge['data']['target'])->toBe('empresa:45456767');

            return true;
        });
});

it('lists the connected entities in the report with their link type', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase(); // centro + sócios MARIA (PF)/HOLDING (PJ) + EMPRESA GRUPO

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->assertSee('Ligações')
        ->assertViewHas('connections', function (array $connections): bool {
            $byName = collect($connections)->keyBy('nome');

            expect($byName['EMPRESA GRUPO']->tipo)->toBe('Grupo econômico')
                ->and($byName['HOLDING']->tipo)->toBe('Sócio')
                ->and($byName['MARIA']->tipo)->toBe('Sócio')
                ->and($byName['MARIA']->documento)->toBe('***111**');

            return true;
        });
});

it('includes same-address neighbours in the report only when the toggle is on', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase(); // centro no CEP 49019900, nº 100

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '45456767', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02', 'cep' => '49019900', 'numero' => 'Nº 100'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => '45456767', 'razao_social' => 'VIZINHA DE ENDERECO']);

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->assertViewHas('connections', fn (array $c): bool => collect($c)->firstWhere('nome', 'VIZINHA DE ENDERECO') === null)
        ->call('toggleAddress')
        ->assertViewHas('connections', function (array $c): bool {
            $vizinha = collect($c)->firstWhere('nome', 'VIZINHA DE ENDERECO');
            expect($vizinha)->not->toBeNull()
                ->and($vizinha->tipo)->toBe('Mesmo endereço');

            return true;
        });
});

it('exports the connections report to Excel', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->call('exportConnections')
        ->assertFileDownloaded();
});

it('labels the person\'s companies as "Empresa do sócio" in the report', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase(); // MARIA (***111**) é sócia da CENTRO e da GRUPO

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->call('focusPerson', '***111**', 'MARIA')
        ->assertViewHas('connections', function (array $connections): bool {
            $tipoPorNome = collect($connections)->keyBy('nome')->map(fn ($c): string => $c->tipo);
            expect($tipoPorNome['EMPRESA CENTRO'])->toBe('Empresa do sócio')
                ->and($tipoPorNome['EMPRESA GRUPO'])->toBe('Empresa do sócio');

            return true;
        });
});

it('builds the copy text grouped by link type in pt-BR', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->assertViewHas('connectionsText', function (string $text): bool {
            expect($text)->toContain('Ligações societárias')
                ->and($text)->toContain('Sócio:')
                ->and($text)->toContain('MARIA')
                ->and($text)->toContain('Grupo econômico:')
                ->and($text)->toContain('EMPRESA GRUPO');

            return true;
        });
});

it('toggles the negative-situation companies on the canvas', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->assertSet('showNegative', true)
        ->assertSee('Ocultar situação negativa')
        ->call('toggleNegative')
        ->assertSet('showNegative', false)
        ->assertSee('Mostrar situação negativa')
        ->assertDispatched('grifo-negative', on: false);
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
        ->call('focusOn', $grupoCnpj)
        ->assertSee('EMPRESA GRUPO')          // novo centro
        ->assertSee('PEDRO')                  // sócio do novo centro
        ->assertSee('Voltar à empresa monitorada')
        ->call('resetFocus')
        ->assertSee('EMPRESA CENTRO');        // voltou ao início
});

it('rate-limits graph pivots to curb scraping', function () {
    config()->set('cnpj.graph.focus_per_minute', 1); // 1 pivô por minuto
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    $grupoCnpj = Cnpj::matrizFromBasico('44555666');
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => substr((string) $grupoCnpj, 12, 2), 'situacao_cadastral' => '02',
    ]);

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->call('focusOn', $grupoCnpj)               // 1º pivô — permitido
        ->assertSet('focus', preg_replace('/\D/', '', (string) $grupoCnpj))
        ->call('focusPerson', '***111**', 'MARIA')  // 2º — estoura o limite
        ->assertViewHas('personMode', false)        // NÃO pivotou para a pessoa
        ->assertSet('focusDocument', '')
        ->assertSet('focusLimited', true)
        ->assertSee('Muitas navegações');           // aviso exibido
});

it('does not rate-limit pivots when the limit is disabled (0)', function () {
    config()->set('cnpj.graph.focus_per_minute', 0);
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    $grupoCnpj = Cnpj::matrizFromBasico('44555666');
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => substr((string) $grupoCnpj, 12, 2), 'situacao_cadastral' => '02',
    ]);

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->call('focusOn', $grupoCnpj)
        ->call('focusPerson', '***111**', 'MARIA') // 2º pivô ainda passa
        ->assertViewHas('personMode', true)
        ->assertSet('focusLimited', false);
});

it('does not rate-limit resetFocus (back to the own portfolio company)', function () {
    config()->set('cnpj.graph.focus_per_minute', 1);
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    $grupoCnpj = Cnpj::matrizFromBasico('44555666');
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => substr((string) $grupoCnpj, 12, 2), 'situacao_cadastral' => '02',
    ]);

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->call('focusOn', $grupoCnpj)   // consome a única cota
        ->call('resetFocus')            // NÃO conta no limite → volta à monitorada
        ->assertSet('focus', '')
        ->assertSet('focusLimited', false)
        ->assertSee('EMPRESA CENTRO');
});

it('ignores a focus with an invalid CNPJ', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->call('focusOn', '123')
        ->assertSee('EMPRESA CENTRO'); // permanece na empresa monitorada
});

it('recenters on a person (PF), showing every company they belong to', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase(); // MARIA (***111**) é sócia da CENTRO e da GRUPO

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->call('focusPerson', '***111**', 'MARIA')
        ->assertSee('MARIA')                       // pessoa no centro
        ->assertSee('EMPRESA CENTRO')              // empresa dela
        ->assertSee('EMPRESA GRUPO')               // outra empresa dela
        ->assertSee('empresa(s) desta pessoa')     // texto do modo pessoa
        ->assertDontSee('Beneficiários finais')    // painel oculto no modo pessoa
        ->call('resetFocus')
        ->assertSee('Beneficiários finais');       // voltou ao modo empresa
});

it('ignores a person focus with an invalid document', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase();

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->call('focusPerson', 'abc', 'X') // sanitiza para vazio → ignora
        ->assertSee('Beneficiários finais'); // segue no modo empresa
});

it('degrades to the unavailable notice in person mode when the base is down', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = monitoredCompanyFor($org);
    bootCompanyGraphBase(withData: false); // tabelas ausentes → forPerson falha

    Livewire::actingAs($user)->test(Graph::class, ['company' => $company])
        ->call('focusPerson', '***111**', 'MARIA')
        ->assertSee('Grafo indisponível');
});
