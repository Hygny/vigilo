<?php

declare(strict_types=1);

use App\Livewire\Companies\Graph;
use App\Livewire\Empresas\Grafo;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Support\Cnpj;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

/**
 * Base CNPJ (sqlite) do grafo: EMPRESA CENTRO (sócios MARIA PF + HOLDING PJ),
 * EMPRESA GRUPO (mesma HOLDING) e INVESTIDA (o CENTRO é sócio PJ dela).
 */
function seedGrafoPublico(): void
{
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');

    $schema->create('estabelecimentos', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'cnpj_ordem', 'cnpj_dv', 'situacao_cadastral', 'nome_fantasia', 'cep', 'numero', 'complemento', 'municipio', 'uf'] as $c) {
            $t->string($c)->nullable();
        }
    });
    $schema->create('empresas', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('razao_social')->nullable();
    });
    $schema->create('socios', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'nome_socio', 'cnpj_cpf_do_socio', 'identificador_de_socio', 'qualificacao_do_socio', 'data_entrada_sociedade'] as $c) {
            $t->string($c)->nullable();
        }
    });
    foreach (['qualificacoes_socios', 'municipios'] as $tabela) {
        $schema->create($tabela, function (Blueprint $t): void {
            $t->string('codigo');
            $t->string('descricao')->nullable();
        });
    }

    $db = DB::connection('cnpj');
    $db->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
        ['cnpj_basico' => '20000001', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02'],
    ]);
    $db->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA CENTRO'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA GRUPO'],
        ['cnpj_basico' => '20000001', 'razao_social' => 'INVESTIDA SA'],
    ]);
    $db->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111222**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'HOLDING', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '20000001', 'nome_socio' => 'EMPRESA CENTRO', 'cnpj_cpf_do_socio' => '11222333000181', 'identificador_de_socio' => '1'],
    ]);
}

function grafoUser(): User
{
    return User::factory()->for(Organization::factory())->create();
}

it('opens the graph of any CNPJ of the base, outside the portfolio', function () {
    seedGrafoPublico();

    $this->actingAs(grafoUser())
        ->get(route('empresas.grafo', '11222333000181'))
        ->assertOk()
        ->assertSee('Grafo societário')
        ->assertSee('EMPRESA CENTRO')
        ->assertSee('Voltar ao perfil')
        ->assertSee(route('empresas.perfil', '11222333000181'), false)
        ->assertSee('1 participação(ões)')
        ->assertSee('Beneficiários finais (estrutura)');
});

it('classifies the participations apart from the economic group in the report', function () {
    seedGrafoPublico();

    $component = Livewire::actingAs(grafoUser())->test(Grafo::class, ['cnpj' => '11222333000181']);

    $tipos = collect($component->viewData('connections'))->mapWithKeys(fn ($c) => [$c->nome => $c->tipo]);

    expect($tipos->get('INVESTIDA SA'))->toBe('Participação')
        ->and($tipos->get('EMPRESA GRUPO'))->toBe('Grupo econômico')
        ->and($tipos->get('HOLDING'))->toBe('Sócio')
        ->and($component->viewData('participationCount'))->toBe(1)
        ->and($component->viewData('groupCount'))->toBe(1)
        ->and($component->viewData('connectionsText'))->toContain("Participação:\n- INVESTIDA SA");
});

it('pivots and resets back to the root CNPJ of the page', function () {
    seedGrafoPublico();

    Livewire::actingAs(grafoUser())->test(Grafo::class, ['cnpj' => '11222333000181'])
        ->call('focusOn', Cnpj::matrizFromBasico('20000001'))
        ->assertSet('focus', Cnpj::matrizFromBasico('20000001'))
        ->assertSee('Voltar à empresa')
        ->call('resetFocus')
        ->assertSet('focus', '')
        ->assertViewHas('focused', false);
});

it('404s an invalid CNPJ and 403s a user without organization', function () {
    seedGrafoPublico();

    $this->actingAs(grafoUser())->get('/empresas/11222333000182/grafo')->assertNotFound();

    $semOrg = User::factory()->create(['organization_id' => null]);
    $this->actingAs($semOrg)->get(route('empresas.grafo', '11222333000181'))->assertForbidden();
});

it('shares the profile rate limit: the graph does not bypass the perfil ceiling', function () {
    seedGrafoPublico();
    config()->set('cnpj.perfil.per_minute', 1);
    $user = grafoUser();
    RateLimiter::clear('perfil-empresa:'.$user->getKey());

    $this->actingAs($user)->get(route('empresas.perfil', '11222333000181'))->assertOk();

    $this->actingAs($user)->get(route('empresas.grafo', '11222333000181'))
        ->assertOk()
        ->assertSee('Muitos perfis abertos')
        ->assertDontSee('Grafo indisponível')
        ->assertDontSee('x-data="grifo(', false);
});

it('keeps the portfolio graph authorized by the monitored company', function () {
    seedGrafoPublico();
    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    Livewire::actingAs(User::factory()->for($org)->create())->test(Graph::class, ['company' => $company])
        ->assertSee('Voltar à empresa')
        ->assertViewHas('participationCount', 1);

    $this->actingAs(grafoUser())->get(route('companies.graph', $company))->assertForbidden();
});

it('refuses a focus change sent straight from the client (only the rate-limited actions move it)', function () {
    seedGrafoPublico();

    $component = Livewire::actingAs(grafoUser())->test(Grafo::class, ['cnpj' => '11222333000181']);

    foreach (['focus' => Cnpj::matrizFromBasico('20000001'), 'focusDocument' => '***111222**', 'focusName' => 'MARIA'] as $prop => $valor) {
        expect(fn () => $component->set($prop, $valor))
            ->toThrow(CannotUpdateLockedPropertyException::class);
    }
});

it('marks the participation count with "+" when the graph hits its cap', function () {
    seedGrafoPublico();
    config()->set('cnpj.graph.reverse_limit', 1);

    $this->actingAs(grafoUser())
        ->get(route('empresas.grafo', '11222333000181'))
        ->assertSee('1+ participação(ões)');
});
