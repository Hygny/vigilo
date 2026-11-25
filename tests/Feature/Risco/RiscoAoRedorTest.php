<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\NivelRisco;
use App\Enums\Severity;
use App\Livewire\Companies\RiscoPanel;
use App\Livewire\Empresas\Perfil;
use App\Livewire\Empresas\RiscoPerfil;
use App\Models\CompanySnapshot;
use App\Models\DirtyListEmployer;
use App\Models\MaintenanceRun;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Notifications\CompanyChangeDetected;
use App\Services\Admin\StatusRotinas;
use App\Services\Risco\DirtyListLookup;
use App\Services\Risco\DirtyListMonitor;
use App\Services\Risco\RiscoAoRedor;
use App\Services\Risco\RiscoAoRedorMonitor;
use App\Services\Risco\RiscoAssessor;
use App\Services\Risco\SancoesLocais;
use App\Support\Cnpj;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

/** Cópia local da CGU "carregada" (as três fontes) num dia. */
function cguCarregada(string $dia = '2026-11-24'): void
{
    foreach (['ceis', 'cnep', 'cepim'] as $fonte) {
        DB::table('cgu_cargas')->insert(['fonte' => $fonte, 'data_arquivo' => $dia, 'registros' => 1, 'carregado_em' => now()]);
    }
}

function cguSancao(string $cnpj, string $fonte = 'ceis', ?string $fim = null, string $inicio = '2024-01-01'): void
{
    DB::table('cgu_sancoes')->insert([
        'fonte' => $fonte, 'cnpj' => $cnpj, 'cnpj_basico' => substr($cnpj, 0, 8), 'nome' => 'X',
        'categoria' => 'Impedimento de contratar', 'orgao' => 'Ministério X', 'data_inicio' => $inicio, 'data_fim' => $fim,
    ]);
}

/**
 * Base CNPJ (sqlite): CENTRO (11222333) com sócio PJ HOLDING (99888777, raiz de
 * 8 díg — formato da base), GRUPO (44555666, mesma holding), INVESTIDA
 * (20000001, o centro é sócio) e LIMPA (30000002, outra investida, ativa).
 */
function seedRiscoAoRedor(): void
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
    createPgfnTables();

    $db = DB::connection('cnpj');
    $db->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02'],
        ['cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '08'], // GRUPO baixada
        ['cnpj_basico' => '20000001', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02'],
        ['cnpj_basico' => '30000002', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02'],
        ['cnpj_basico' => '99888777', 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '04'], // HOLDING inapta
    ]);
    $db->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'CENTRO SA'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'GRUPO LTDA'],
        ['cnpj_basico' => '20000001', 'razao_social' => 'INVESTIDA SA'],
        ['cnpj_basico' => '30000002', 'razao_social' => 'LIMPA SA'],
        ['cnpj_basico' => '99888777', 'razao_social' => 'HOLDING SA'],
    ]);
    $db->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING SA', 'cnpj_cpf_do_socio' => '99888777', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '11222333', 'nome_socio' => 'FULANO', 'cnpj_cpf_do_socio' => '***123456**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'HOLDING SA', 'cnpj_cpf_do_socio' => '99888777', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '20000001', 'nome_socio' => 'CENTRO SA', 'cnpj_cpf_do_socio' => '11222333', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '30000002', 'nome_socio' => 'CENTRO SA', 'cnpj_cpf_do_socio' => '11222333', 'identificador_de_socio' => '1'],
    ]);

    seedPgfnDivida(Cnpj::matrizFromBasico('44555666') ?? '', 5000.0); // GRUPO deve
}

it('flags the linked companies by certain links with their risk signals', function () {
    seedRiscoAoRedor();
    cguCarregada();
    cguSancao(Cnpj::matrizFromBasico('20000001') ?? ''); // INVESTIDA: sanção vigente
    cguSancao(Cnpj::matrizFromBasico('30000002') ?? '', fim: '2020-01-01'); // LIMPA: sanção já encerrada → não conta
    DirtyListEmployer::query()->create(['documento' => '99888777000250', 'nome' => 'HOLDING', 'imported_at' => now()]); // filial da HOLDING listada

    $r = app(RiscoAoRedor::class)->para('11222333000181');

    expect($r->total())->toBe(4) // HOLDING, GRUPO, INVESTIDA, LIMPA (o PF não entra)
        ->and($r->pgfnConsultada)->toBeTrue()
        ->and($r->sancoesConsultadas)->toBeTrue();

    $porNome = collect($r->comAlerta())->keyBy('nome');
    expect($porNome->keys()->all())->toBe(['HOLDING SA', 'INVESTIDA SA', 'GRUPO LTDA']) // Lista Suja > sanção > dívida
        ->and($porNome['HOLDING SA']->ligacao)->toBe('Sócio PJ')
        ->and($porNome['HOLDING SA']->sinais())->toBe(['Lista Suja (trabalho escravo)', 'situação INAPTA'])
        ->and($porNome['INVESTIDA SA']->ligacao)->toBe('Participação')
        ->and($porNome['INVESTIDA SA']->sinais())->toBe(['sanção vigente (CGU)'])
        ->and($porNome['GRUPO LTDA']->ligacao)->toBe('Grupo econômico')
        ->and($porNome['GRUPO LTDA']->sinais())->toBe(['dívida ativa (PGFN)', 'situação BAIXADA'])
        ->and($r->selo())->toBe('3 de 4 empresas ligadas com Lista Suja, sanção, dívida ativa ou situação negativa');
});

it('says which sources were not checked instead of implying the neighbours are clean', function () {
    seedRiscoAoRedor();
    Schema::connection('cnpj')->drop('pgfn_dividas'); // PGFN fora; cópia da CGU nunca carregada

    $r = app(RiscoAoRedor::class)->para('11222333000181');

    expect($r->pgfnConsultada)->toBeFalse()
        ->and($r->sancoesConsultadas)->toBeFalse()
        ->and($r->naoConsultadas())->toBe(['sanções (CGU)', 'dívida ativa (PGFN)']);
});

it('answers "no sanction" from the local copy only after the three sources were loaded', function () {
    $s = app(SancoesLocais::class);

    expect($s->disponivel())->toBeFalse()
        ->and($s->relatorio('11222333000181')->disponivel)->toBeFalse();

    cguCarregada('2026-11-10');
    cguSancao('11222333000262', 'cnep'); // filial sancionada → vale para a raiz

    $rel = $s->relatorio('11222333000181');
    expect($rel->disponivel)->toBeTrue()
        ->and($rel->temSancaoAtiva())->toBeTrue()
        ->and($rel->sancoes[0]->fonte->value)->toBe('cnep')
        ->and($s->referencia())->toBe('2026-11-10');

    Carbon::setTestNow('2026-11-25');
    expect($s->desatualizada())->toBeTrue();
    Carbon::setTestNow('2026-11-12');
    expect($s->desatualizada())->toBeFalse();
    Carbon::setTestNow();
});

it('finds the dirty list entry by the CNPJ root, preferring the exact CNPJ', function () {
    DirtyListEmployer::query()->create(['documento' => '11222333000262', 'nome' => 'FILIAL', 'imported_at' => now()]);
    DirtyListEmployer::query()->create(['documento' => '11222333000181', 'nome' => 'MATRIZ', 'imported_at' => now()]);
    DirtyListEmployer::query()->create(['documento' => '11222333000', 'nome' => 'CPF COM MESMO PREFIXO', 'imported_at' => now()]);
    $lookup = app(DirtyListLookup::class);

    expect($lookup->consta('11222333000181')?->nome)->toBe('MATRIZ')
        ->and($lookup->consta('11222333000343')?->nome)->toBe('FILIAL') // outra filial: acha pela raiz
        ->and($lookup->consta('99888777000166'))->toBeNull()
        ->and(array_map('strval', array_keys($lookup->raizesConstantes(['11222333', '99888777', 'lixo']))))->toBe(['11222333']);
});

it('rates any CNPJ with the same rule, using the local copy for the sanctions', function () {
    seedRiscoAoRedor();
    $assessor = app(RiscoAssessor::class);

    // Cópia não carregada e empresa limpa: não diz "baixo" sem ter checado.
    expect($assessor->paraPublico('30000002000100', 'ATIVA')->nivel)->toBeNull();

    cguCarregada();
    expect($assessor->paraPublico('30000002000100', 'ATIVA')->nivel)->toBe(NivelRisco::Baixo);

    cguSancao('30000002000100');
    expect($assessor->paraPublico('30000002000100', 'ATIVA')->nivel)->toBe(NivelRisco::Alto);
    expect($assessor->paraPublico('44555666000100', 'BAIXADA')->nivel)->toBe(NivelRisco::Medio); // dívida + baixada
});

it('shows the risk card of the public profile, with the copy date and the neighbours seal', function () {
    seedRiscoAoRedor();
    cguCarregada();
    cguSancao(Cnpj::matrizFromBasico('20000001') ?? '');
    $user = User::factory()->for(Organization::factory())->create();

    Livewire::actingAs($user)->test(RiscoPerfil::class, ['cnpj' => '11222333000181', 'situacao' => 'ATIVA'])
        ->assertSee('Risco Baixo')
        ->assertSee('Nenhuma sanção encontrada')
        ->assertSee('cópia diária do Portal da Transparência (CGU) de 24/11/2026')
        ->assertSee('Risco ao redor')
        ->assertSee('3 de 4 empresas ligadas com sanção, dívida ativa ou situação negativa')
        ->assertSee('INVESTIDA SA')
        ->assertSee(route('empresas.perfil', Cnpj::matrizFromBasico('20000001')), false)
        ->assertDontSee('Dívida ativa na União (PGFN).'); // o perfil tem card próprio
});

it('shows the neighbours seal on the portfolio risk panel too', function () {
    seedRiscoAoRedor();
    cguCarregada();
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(fn () => Http::response([]));
    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    Livewire::actingAs(User::factory()->for($org)->create())->test(RiscoPanel::class, ['company' => $company])
        ->assertSee('Risco ao redor')
        ->assertSee('2 de 4 empresas ligadas com dívida ativa ou situação negativa');
});

it('degrades the neighbours section when the CNPJ base is down', function () {
    useCnpjSqlite(); // sem tabelas
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(fn () => Http::response([]));
    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    Livewire::actingAs(User::factory()->for($org)->create())->test(RiscoPanel::class, ['company' => $company])
        ->assertSee('Risco ao redor')
        ->assertSee('Indisponível no momento.');
});

it('never shows the neighbours as clean (green) when a source was not checked or the copy is stale', function () {
    seedRiscoAoRedor();
    // Só ligadas limpas: tira a dívida do GRUPO e deixa todas ativas.
    DB::connection('cnpj')->table('pgfn_dividas')->delete();
    DB::connection('cnpj')->table('estabelecimentos')->update(['situacao_cadastral' => '02']);
    $user = User::factory()->for(Organization::factory())->create();

    // Cópia da CGU nunca carregada → sanções não consultadas: tom neutro.
    Livewire::actingAs($user)->test(RiscoPerfil::class, ['cnpj' => '11222333000181', 'situacao' => 'ATIVA'])
        ->assertSee('nas fontes que puderam ser consultadas')
        ->assertSee('Não consultado agora: sanções (CGU).')
        ->assertDontSee('tem sinal de risco nas fontes consultadas');

    // Carregada mas velha → também neutro, com aviso; e o "baixo" ganha a ressalva.
    cguCarregada('2026-10-01');
    Carbon::setTestNow('2026-11-25');
    Livewire::actingAs($user)->test(RiscoPerfil::class, ['cnpj' => '11222333000181', 'situacao' => 'ATIVA'])
        ->assertSee('Risco Baixo')
        ->assertSee('A cópia das sanções da CGU está desatualizada.')
        ->assertSee('sanção recente pode não aparecer')
        ->assertDontSee('tem sinal de risco nas fontes consultadas');

    // Em dia → aí sim, verde (o resultado anterior, completo, ficou no cache curto).
    Cache::flush();
    DB::table('cgu_cargas')->update(['data_arquivo' => '2026-11-24']);
    Livewire::actingAs($user)->test(RiscoPerfil::class, ['cnpj' => '11222333000181', 'situacao' => 'ATIVA'])
        ->assertSee('Nenhuma das 4 empresa(s) ligada(s) tem sinal de risco nas fontes consultadas');
    Carbon::setTestNow();
});

it('403s the risk card for a user without organization and 404s an invalid CNPJ', function () {
    $semOrg = User::factory()->create(['organization_id' => null]);
    Livewire::actingAs($semOrg)->test(RiscoPerfil::class, ['cnpj' => '11222333000181'])->assertForbidden();

    $user = User::factory()->for(Organization::factory())->create();
    Livewire::actingAs($user)->test(RiscoPerfil::class, ['cnpj' => '11222333000182'])->assertNotFound();
});

it('alerts the portfolio when a branch of the company enters the dirty list', function () {
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for(Organization::factory()))->create(['cnpj' => '11222333000181']);
    DirtyListEmployer::query()->create(['documento' => '11222333000262', 'nome' => 'FAZENDA (FILIAL)', 'imported_at' => now()]);

    $entrada = app(DirtyListMonitor::class)->sync($company);

    expect($entrada?->nome)->toBe('FAZENDA (FILIAL)')
        ->and($company->fresh()->dirty_list_flagged_at)->not->toBeNull();
});

it('caches only the complete "risco ao redor" result, for a short window', function () {
    seedRiscoAoRedor();
    cguCarregada();
    $servico = app(RiscoAoRedor::class);

    expect($servico->para('11222333000181')->comAlerta())->toHaveCount(2); // HOLDING inapta + GRUPO dívida/baixada

    // Muda a base: dentro da janela, vem do cache.
    DB::connection('cnpj')->table('estabelecimentos')->update(['situacao_cadastral' => '02']);
    DB::connection('cnpj')->table('pgfn_dividas')->delete();
    expect($servico->para('11222333000181')->comAlerta())->toHaveCount(2);

    $this->travel(11)->minutes();
    expect($servico->para('11222333000181')->comAlerta())->toBe([]);
});

it('does not cache a partial result (a source was down)', function () {
    seedRiscoAoRedor(); // cópia da CGU nunca carregada → sanções não consultadas
    $servico = app(RiscoAoRedor::class);

    expect($servico->para('11222333000181')->sancoesConsultadas)->toBeFalse();

    cguCarregada();
    expect($servico->para('11222333000181')->sancoesConsultadas)->toBeTrue(); // não ficou preso ao parcial
});

/** Empresa da carteira (com snapshot) apontando para o CENTRO da base de teste. */
function carteiraComCentro(): MonitoredCompany
{
    $org = Organization::factory()->create();
    User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);
    CompanySnapshot::factory()->create(['monitored_company_id' => $company->id]);

    return $company;
}

it('takes a silent baseline, then flags only NEW signals of the linked companies', function () {
    seedRiscoAoRedor();
    cguCarregada();
    $company = carteiraComCentro();
    $monitor = app(RiscoAoRedorMonitor::class);

    // 1ª rodada: baseline (HOLDING inapta + GRUPO dívida/baixada já existiam) — sem alerta.
    expect($monitor->aplicar($company, $monitor->consultar($company)))->toBe([])
        ->and($company->neighborFlags()->pluck('sinais', 'cnpj_basico')->all())->toEqualCanonicalizing(['99888777' => 'situacao', '44555666' => 'divida,situacao'])
        ->and($company->fresh()->neighbor_risk_synced_at)->not->toBeNull();

    // A INVESTIDA ganha uma sanção e o GRUPO entra na Lista Suja.
    cguSancao(Cnpj::matrizFromBasico('20000001') ?? '');
    DirtyListEmployer::query()->create(['documento' => Cnpj::matrizFromBasico('44555666'), 'imported_at' => now()]);
    Cache::flush(); // o "ao redor" tem cache curto

    $novidades = collect($monitor->aplicar($company, $monitor->consultar($company)))
        ->mapWithKeys(fn (array $n) => [$n['empresa']->nome => $n['novos']]);

    expect($novidades->all())->toEqualCanonicalizing(['INVESTIDA SA' => ['sancao'], 'GRUPO LTDA' => ['lista_suja']]);

    // Mesma situação de novo: nada novo.
    Cache::flush();
    expect($monitor->aplicar($company, $monitor->consultar($company)))->toBe([]);
});

it('forgets a signal that went away and alerts again if it comes back', function () {
    seedRiscoAoRedor();
    cguCarregada();
    $company = carteiraComCentro();
    $monitor = app(RiscoAoRedorMonitor::class);
    $monitor->aplicar($company, $monitor->consultar($company)); // baseline

    DB::connection('cnpj')->table('pgfn_dividas')->delete(); // GRUPO quitou
    Cache::flush();
    expect($monitor->aplicar($company, $monitor->consultar($company)))->toBe([])
        ->and($company->neighborFlags()->where('cnpj_basico', '44555666')->value('sinais'))->toBe('situacao');

    seedPgfnDivida(Cnpj::matrizFromBasico('44555666') ?? '', 100.0); // voltou a dever
    Cache::flush();
    expect(collect($monitor->aplicar($company, $monitor->consultar($company)))->pluck('novos')->all())->toBe([['divida']]);
});

it('never compares with an incomplete report (a source was down)', function () {
    seedRiscoAoRedor(); // cópia da CGU nunca carregada → sanções não consultadas
    $company = carteiraComCentro();

    expect(app(RiscoAoRedorMonitor::class)->consultar($company))->toBeNull()
        ->and($company->neighborFlags()->count())->toBe(0)
        ->and($company->fresh()->neighbor_risk_synced_at)->toBeNull();
});

it('turns new neighbour signals into inbox alerts (and notifies), recording the run', function () {
    Notification::fake();
    seedRiscoAoRedor();
    cguCarregada();
    $company = carteiraComCentro();

    $this->artisan('vigilo:varrer-risco-ao-redor')->assertSuccessful(); // baseline
    expect($company->changeEvents()->count())->toBe(0);

    cguSancao(Cnpj::matrizFromBasico('20000001') ?? '');
    Cache::flush();
    $this->artisan('vigilo:varrer-risco-ao-redor')->assertSuccessful(); // já checada hoje: pula
    expect($company->changeEvents()->count())->toBe(0);

    $this->artisan('vigilo:varrer-risco-ao-redor', ['--all' => true])->assertSuccessful();

    $evento = $company->changeEvents()->sole();
    expect($evento->type)->toBe(ChangeType::RiscoAoRedor)
        ->and($evento->severity)->toBe(Severity::High)
        ->and($evento->new_value)->toContain('INVESTIDA SA')
        ->and($evento->new_value)->toContain('Participação')
        ->and($evento->new_value)->toContain('sanção vigente (CGU)')
        ->and(MaintenanceRun::ultima('risco_ao_redor')?->message)->toContain('1 alerta(s)');

    Notification::assertSentTimes(CompanyChangeDetected::class, 1);
});

it('aborts the sweep when the CNPJ base is down (breaker) and records the failure', function () {
    useCnpjSqlite(); // sem tabelas
    config()->set('risco.sweep.max_consecutive_failures', 1);
    carteiraComCentro();

    $this->artisan('vigilo:varrer-risco-ao-redor')->assertFailed();

    expect(MaintenanceRun::ultima('risco_ao_redor')?->isSuccess())->toBeFalse();
});

it('is scheduled twice a day in Brasília time, after the CGU copy', function () {
    $evento = collect(app(Schedule::class)->events())
        ->first(fn ($e) => str_contains((string) $e->command, 'vigilo:varrer-risco-ao-redor'));

    expect($evento?->expression)->toBe('30 9,12 * * *')
        ->and($evento?->timezone)->toBe('America/Sao_Paulo')
        ->and($evento?->withoutOverlapping)->toBeTrue();
});

it('forgets a linked company only after it is missing for 3 rounds in a row (no alert storm)', function () {
    seedRiscoAoRedor();
    cguCarregada();
    $company = carteiraComCentro();
    $monitor = app(RiscoAoRedorMonitor::class);
    $rodada = function () use ($monitor, $company): array {
        Cache::flush();

        return $monitor->aplicar($company, $monitor->consultar($company));
    };
    $rodada(); // baseline: HOLDING (situação) conhecida

    $sociosHolding = DB::connection('cnpj')->table('socios')->where('cnpj_cpf_do_socio', '99888777')->get()->map(fn ($r) => (array) $r)->all();
    DB::connection('cnpj')->table('socios')->where('cnpj_cpf_do_socio', '99888777')->delete(); // some do grafo de passagem

    expect($rodada())->toBe([])
        ->and($company->neighborFlags()->where('cnpj_basico', '99888777')->value('ausencias'))->toBe(1);

    DB::connection('cnpj')->table('socios')->insert($sociosHolding); // volta: não é "novo"
    expect($rodada())->toBe([])
        ->and($company->neighborFlags()->where('cnpj_basico', '99888777')->value('ausencias'))->toBe(0);

    DB::connection('cnpj')->table('socios')->where('cnpj_cpf_do_socio', '99888777')->delete(); // saiu de vez
    $rodada();
    $rodada();
    $rodada();
    expect($company->neighborFlags()->where('cnpj_basico', '99888777')->exists())->toBeFalse();
});

it('drops the flag of a linked company that became clean', function () {
    seedRiscoAoRedor();
    cguCarregada();
    $company = carteiraComCentro();
    $monitor = app(RiscoAoRedorMonitor::class);
    $monitor->aplicar($company, $monitor->consultar($company)); // GRUPO: dívida + baixada

    DB::connection('cnpj')->table('pgfn_dividas')->delete();
    DB::connection('cnpj')->table('estabelecimentos')->where('cnpj_basico', '44555666')->update(['situacao_cadastral' => '02']);
    Cache::flush();

    expect($monitor->aplicar($company, $monitor->consultar($company)))->toBe([])
        ->and($company->neighborFlags()->where('cnpj_basico', '44555666')->exists())->toBeFalse();
});

it('rates a new debt/situation signal as medium severity and lists the routine on the card', function () {
    Notification::fake();
    seedRiscoAoRedor();
    cguCarregada();
    $company = carteiraComCentro();
    $this->artisan('vigilo:varrer-risco-ao-redor')->assertSuccessful(); // baseline

    seedPgfnDivida(Cnpj::matrizFromBasico('20000001') ?? '', 50.0); // INVESTIDA passa a dever
    Cache::flush();
    $this->artisan('vigilo:varrer-risco-ao-redor', ['--all' => true])->assertSuccessful();

    expect($company->changeEvents()->sole()->severity)->toBe(Severity::Medium);

    $linha = collect(app(StatusRotinas::class)->todas())->firstWhere('nome', 'Risco das empresas ligadas (alertas)');
    expect($linha?->situacao)->toBe('ok');
});

it('does not compare when the PJ partners situation could not be read', function () {
    seedRiscoAoRedor();
    cguCarregada();
    $company = carteiraComCentro();
    DB::connection('cnpj')->beforeExecuting(function (string $sql): void {
        if (str_starts_with($sql, 'select "situacao_cadastral", "cnpj_basico" from "estabelecimentos"')) {
            throw new RuntimeException('base caiu no meio');
        }
    });

    $report = app(RiscoAoRedor::class)->para('11222333000181');

    expect($report->situacaoConsultada)->toBeFalse()
        ->and($report->naoConsultadas())->toContain('situação dos sócios PJ')
        ->and(app(RiscoAoRedorMonitor::class)->consultar($company))->toBeNull();
});
