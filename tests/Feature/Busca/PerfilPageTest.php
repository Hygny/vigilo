<?php

declare(strict_types=1);

use App\Livewire\Empresas\Perfil;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

/** Base CNPJ (sqlite) com o schema que o EmpresaLookup lê + a PADARIA AURORA (MEI-like). */
function seedPerfilBase(bool $comPgfn = true, bool $comHistorico = true): void
{
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');

    $schema->create('estabelecimentos', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'cnpj_ordem', 'cnpj_dv', 'situacao_cadastral', 'data_situacao_cadastral', 'data_inicio_atividade',
            'nome_fantasia', 'cnae_fiscal_principal', 'tipo_logradouro', 'logradouro', 'numero', 'complemento', 'bairro', 'cep', 'municipio', 'uf'] as $c) {
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
    foreach (['municipios', 'cnaes', 'qualificacoes_socios'] as $tabela) {
        $schema->create($tabela, function (Blueprint $t): void {
            $t->string('codigo');
            $t->string('descricao')->nullable();
        });
    }

    $db = DB::connection('cnpj');
    $db->table('estabelecimentos')->insert([
        'cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02',
        'data_situacao_cadastral' => '2020-01-01', 'data_inicio_atividade' => '2020-01-01', 'nome_fantasia' => 'PAO DA AURORA',
        'cnae_fiscal_principal' => '1091102', 'tipo_logradouro' => 'RUA', 'logradouro' => 'DAS FLORES', 'numero' => '10',
        'bairro' => 'CENTRO', 'cep' => '01001000', 'municipio' => '7107', 'uf' => 'SP',
    ]);
    $db->table('empresas')->insert(['cnpj_basico' => '11222333', 'razao_social' => 'MARIA SOUZA 12345678901']);
    $db->table('socios')->insert([
        'cnpj_basico' => '11222333', 'nome_socio' => 'MARIA SOUZA', 'cnpj_cpf_do_socio' => '***456789**',
        'identificador_de_socio' => '2', 'qualificacao_do_socio' => '49', 'data_entrada_sociedade' => '2020-01-01',
    ]);
    $db->table('municipios')->insert(['codigo' => '7107', 'descricao' => 'SAO PAULO']);
    $db->table('cnaes')->insert(['codigo' => '1091102', 'descricao' => 'Padaria e confeitaria']);
    $db->table('qualificacoes_socios')->insert(['codigo' => '49', 'descricao' => 'Sócio-Administrador']);

    if ($comPgfn) {
        createPgfnTables();
        seedPgfnDivida('11222333000181', 1500.00, ajuizado: true);
    }

    if ($comHistorico) {
        createHistoricoTables();
        $db->table('historico_execucoes')->insert(['referencia' => '2026-10', 'status' => 'ok']);
        seedHistorico('11222333000181', 'situacao', '04', '02', now()->subMonth()->toDateTimeString());
    }
}

function perfilUser(?Organization $org = null): User
{
    return User::factory()->for($org ?? Organization::factory())->create();
}

it('shows any company of the base with partners, PGFN debt and cadastral history', function () {
    seedPerfilBase();

    $this->actingAs(perfilUser())
        ->get(route('empresas.perfil', '11222333000181'))
        ->assertOk()
        ->assertSee('MARIA SOUZA ***456789**')       // CPF do MEI mascarado no título
        ->assertDontSee('12345678901')
        ->assertSee('PAO DA AURORA')
        ->assertSee('1091-1/02 — Padaria e confeitaria')
        ->assertSee('01001-000')
        ->assertSee('Sócio-Administrador')
        ->assertSee('1 inscrição · R$ 1.500,00')
        ->assertSee('1 ajuizada(s)')
        ->assertSee('Situação cadastral:')
        ->assertSee('INAPTA')
        ->assertSee('registradas desde 10/2026');
});

it('says "no PGFN debt" only when the PGFN was checked, and degrades otherwise', function () {
    seedPerfilBase(comPgfn: false, comHistorico: false);

    $this->actingAs(perfilUser())
        ->get(route('empresas.perfil', '11222333000181'))
        ->assertOk()
        ->assertSee('Não foi possível consultar a dívida ativa agora')
        ->assertDontSee('Nenhuma inscrição em dívida ativa')
        ->assertSee('Histórico indisponível no momento');

    createPgfnTables();

    $this->actingAs(perfilUser())
        ->get(route('empresas.perfil', '11222333000181'))
        ->assertSee('Nenhuma inscrição em dívida ativa');
});

it('adds the company to a portfolio of the organization', function () {
    seedPerfilBase();
    $org = Organization::factory()->create();
    $portfolio = Portfolio::factory()->for($org)->create(['name' => 'Fornecedores']);

    Livewire::actingAs(perfilUser($org))->test(Perfil::class, ['cnpj' => '11222333000181'])
        ->set('portfolioId', $portfolio->id)
        ->call('adicionar')
        ->assertHasNoErrors()
        ->assertSee('Ver na carteira');

    expect($portfolio->monitoredCompanies()->where('cnpj', '11222333000181')->exists())->toBeTrue();
});

it('does not add to a portfolio of another organization', function () {
    seedPerfilBase();
    $alheio = Portfolio::factory()->for(Organization::factory())->create();

    Livewire::actingAs(perfilUser())->test(Perfil::class, ['cnpj' => '11222333000181'])
        ->set('portfolioId', $alheio->id)
        ->call('adicionar')
        ->assertHasErrors('portfolioId');

    expect(MonitoredCompany::query()->count())->toBe(0);
});

it('respects the plan quota when adding from the profile', function () {
    seedPerfilBase();
    config()->set('plans.free.max_companies', 1); // org nova = plano Free
    $org = Organization::factory()->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '99888777000166']);

    Livewire::actingAs(perfilUser($org))->test(Perfil::class, ['cnpj' => '11222333000181'])
        ->set('portfolioId', $portfolio->id)
        ->call('adicionar')
        ->assertHasErrors('portfolioId');

    expect($portfolio->monitoredCompanies()->count())->toBe(1);
});

it('links to the portfolio company when the organization already monitors it', function () {
    seedPerfilBase();
    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    $this->actingAs(perfilUser($org))
        ->get(route('empresas.perfil', '11222333000181'))
        ->assertSee(route('companies.show', $company), false)
        ->assertDontSee('Adicionar à carteira');
});

it('handles a CNPJ outside the base, an invalid CNPJ and a base outage', function () {
    seedPerfilBase();
    $user = perfilUser();

    $this->actingAs($user)->get(route('empresas.perfil', '11444777000161'))->assertOk()->assertSee('CNPJ não encontrado');
    $this->actingAs($user)->get('/empresas/11222333000182')->assertNotFound(); // DV inválido

    useCnpjSqlite(); // sem tabelas
    $this->actingAs($user)->get(route('empresas.perfil', '11222333000181'))->assertSee('Base CNPJ indisponível');
});

it('rate-limits profile views per user', function () {
    seedPerfilBase();
    config()->set('cnpj.perfil.per_minute', 1);
    $user = perfilUser();
    RateLimiter::clear('perfil-empresa:'.$user->getKey());

    $this->actingAs($user)->get(route('empresas.perfil', '11222333000181'))->assertSee('PAO DA AURORA');
    $this->actingAs($user)->get(route('empresas.perfil', '11222333000181'))
        ->assertSee('Muitos perfis abertos')
        ->assertDontSee('PAO DA AURORA');
});

it('requires an organization and a login', function () {
    $this->get(route('empresas.perfil', '11222333000181'))->assertRedirect(route('login'));
    $this->actingAs(User::factory()->superAdmin()->create())->get(route('empresas.perfil', '11222333000181'))->assertForbidden();
});
