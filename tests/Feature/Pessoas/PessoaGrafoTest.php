<?php

declare(strict_types=1);

use App\Livewire\Pessoas\Grafo as PessoasGrafo;
use App\Models\Organization;
use App\Models\PessoaConsulta;
use App\Models\User;
use App\Support\PessoaChave;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

/** Base CNPJ (SQLite) com MARIA SOUZA sócia de duas empresas; devolve a chave dela. */
function grafoPessoaBase(string $documento = '***456789**', string $nome = 'MARIA SOUZA'): string
{
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');
    $schema->create('busca_pessoa', function (Blueprint $t): void {
        foreach (['chave', 'nome', 'nome_exibicao', 'documento', 'tipo'] as $c) {
            $t->string($c)->nullable();
        }
        $t->integer('empresas')->default(0);
    });
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
        foreach (['cnpj_basico', 'nome_socio', 'nome_norm', 'cnpj_cpf_do_socio', 'identificador_de_socio', 'qualificacao_do_socio', 'data_entrada_sociedade'] as $c) {
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
    $chave = PessoaChave::para($documento, $nome);
    $db->table('busca_pessoa')->insert(['chave' => $chave, 'nome' => " {$nome} ", 'nome_exibicao' => $nome, 'documento' => $documento, 'tipo' => $documento === '00000000000000' ? '3' : '2', 'empresas' => 2]);
    foreach (['11222333' => 'PADARIA AURORA LTDA', '44555666' => 'CONFEITARIA FILHA LTDA'] as $basico => $razao) {
        $db->table('empresas')->insert(['cnpj_basico' => $basico, 'razao_social' => $razao]);
        $db->table('estabelecimentos')->insert(['cnpj_basico' => $basico, 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02', 'uf' => 'SP']);
        $db->table('socios')->insert(['cnpj_basico' => $basico, 'nome_socio' => $nome, 'cnpj_cpf_do_socio' => $documento, 'identificador_de_socio' => '2', 'qualificacao_do_socio' => '49']);
    }
    // Homônimo de documento (mesmo CPF mascarado, outro nome): fora do grafo dela.
    $db->table('empresas')->insert(['cnpj_basico' => '77888999', 'razao_social' => 'OUTRA PESSOA LTDA']);
    $db->table('socios')->insert(['cnpj_basico' => '77888999', 'nome_socio' => 'MARIA SOUZA SILVA', 'cnpj_cpf_do_socio' => $documento, 'identificador_de_socio' => '2']);

    return $chave;
}

function grafoPessoaUser(): User
{
    return User::factory()->for(Organization::factory())->create();
}

it('opens the graph centered on the person, with her companies only (document + name)', function () {
    $chave = grafoPessoaBase();
    $user = grafoPessoaUser();

    $card = Livewire::actingAs($user)->test(PessoasGrafo::class, ['chave' => $chave])
        ->assertOk()
        ->assertSee('MARIA SOUZA')
        ->assertSee('Voltar à pessoa')
        ->assertViewHas('personMode', true)
        ->assertViewHas('focused', false);

    $cyto = $card->viewData('cyto');
    expect($cyto['center'])->toBe('socio:***456789**')
        ->and(collect($cyto['nodes'])->pluck('data.title')->filter()->all())->toContain('PADARIA AURORA LTDA', 'CONFEITARIA FILHA LTDA')
        ->and(collect($cyto['nodes'])->pluck('data.title')->all())->not->toContain('OUTRA PESSOA LTDA')
        ->and(PessoaConsulta::query()->where('tipo', PessoaConsulta::TIPO_GRAFO)->where('user_id', $user->id)->count())->toBe(1);
});

it('pivots to a company and comes back to the person', function () {
    $chave = grafoPessoaBase();

    Livewire::actingAs(grafoPessoaUser())->test(PessoasGrafo::class, ['chave' => $chave])
        ->call('focusOn', '11222333000181')
        ->assertViewHas('personMode', false)
        ->assertViewHas('focused', true)
        ->call('resetFocus')
        ->assertViewHas('personMode', true)
        ->assertViewHas('focused', false);
});

it('never lets the client swap the root person', function () {
    $chave = grafoPessoaBase();

    Livewire::actingAs(grafoPessoaUser())->test(PessoasGrafo::class, ['chave' => $chave])
        ->set('documento', '***111111**');
})->throws(CannotUpdateLockedPropertyException::class);

it('has no graph for a foreigner (zeroed document) or an unknown key, and requires an organization', function () {
    $estrangeiro = grafoPessoaBase('00000000000000', 'JOHN SMITH');

    Livewire::actingAs(grafoPessoaUser())->test(PessoasGrafo::class, ['chave' => $estrangeiro])->assertNotFound();
    Livewire::actingAs(grafoPessoaUser())->test(PessoasGrafo::class, ['chave' => PessoaChave::para('***000000**', 'NINGUEM')])->assertNotFound();
    Livewire::actingAs(User::factory()->create(['organization_id' => null]))->test(PessoasGrafo::class, ['chave' => $estrangeiro])->assertForbidden();
});

it('counts on the profile rate limit (anti-scraping)', function () {
    $chave = grafoPessoaBase();
    config()->set('cnpj.perfil.per_minute', 1);
    $user = grafoPessoaUser();

    Livewire::actingAs($user)->test(PessoasGrafo::class, ['chave' => $chave])->assertSet('limitado', false);
    Livewire::actingAs($user)->test(PessoasGrafo::class, ['chave' => $chave])->assertSet('limitado', true);
});

it('links the person page to her graph', function () {
    $chave = grafoPessoaBase();

    test()->actingAs(grafoPessoaUser())->get(route('pessoas.perfil', $chave))
        ->assertOk()
        ->assertSee(route('pessoas.grafo', $chave), false)
        ->assertSee('Ver no grafo societário');
});

it('has no graph for a person without document, and hides the link for a foreigner', function () {
    $chave = grafoPessoaBase();
    DB::connection('cnpj')->table('busca_pessoa')->update(['documento' => null]);
    Livewire::actingAs(grafoPessoaUser())->test(PessoasGrafo::class, ['chave' => $chave])->assertNotFound();

    DB::connection('cnpj')->table('busca_pessoa')->update(['documento' => '***456789**', 'tipo' => '3']); // estrangeiro com documento não zerado
    Livewire::actingAs(grafoPessoaUser())->test(PessoasGrafo::class, ['chave' => $chave])->assertNotFound();
    test()->actingAs(grafoPessoaUser())->get(route('pessoas.perfil', $chave))->assertOk()->assertDontSee('Ver no grafo societário');
});
