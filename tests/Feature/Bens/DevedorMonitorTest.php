<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Jobs\SincronizarDevedorJob;
use App\Livewire\Companies\Recuperacao;
use App\Livewire\Companies\Show;
use App\Livewire\Empresas\IndiciosEsvaziamento;
use App\Models\ChangeEvent;
use App\Models\CompanySnapshot;
use App\Models\MaintenanceRun;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\RecuperacaoItem;
use App\Models\User;
use App\Notifications\CompanyChangeDetected;
use App\Services\Admin\StatusRotinas;
use App\Services\Bens\AeronavesLookup;
use App\Services\Bens\DevedorMonitor;
use App\Services\Bens\IndiciosEsvaziamento as IndiciosService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

/** Base CNPJ mínima (SQLite) com a devedora PADARIA AURORA, 1ª inscrição na PGFN em 2020. */
function devBase(): void
{
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');
    $schema->create('estabelecimentos', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'cnpj_ordem', 'cnpj_dv', 'situacao_cadastral', 'data_situacao_cadastral', 'data_inicio_atividade', 'cnae_fiscal_principal',
            'cep', 'numero', 'complemento', 'ddd_1', 'telefone_1', 'correio_eletronico', 'uf'] as $c) {
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
    $schema->create('qualificacoes_socios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    createPgfnTables();
    createHistoricoTables();

    devEmpresa('11222333', 'PADARIA AURORA LTDA', '2012-01-01');
    devSocio('11222333', 'MARIA SOUZA', '***456789**', '2012-01-01');
    DB::connection('cnpj')->table('pgfn_dividas')->insert([
        'cnpj' => '11222333000181', 'cnpj_basico' => '11222333', 'tipo' => 'nao-previdenciario', 'situacao' => 'ATIVA',
        'data_inscricao' => '2020-03-01', 'ajuizado' => false, 'valor' => 1000,
    ]);
    config()->set('bens.esvaziamento.cache_minutos', 0);
    app()->forgetInstance(IndiciosService::class);
}

function devEmpresa(string $basico, string $razao, string $inicio, string $cnae = '1091102'): void
{
    DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => $basico, 'razao_social' => $razao]);
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => $basico, 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02',
        'data_inicio_atividade' => $inicio, 'cnae_fiscal_principal' => $cnae, 'cep' => '0'.substr($basico, 0, 7), 'numero' => '1',
    ]);
}

function devSocio(string $basico, string $nome, string $doc, string $entrada, string $identificador = '2'): void
{
    DB::connection('cnpj')->table('socios')->insert([
        'cnpj_basico' => $basico, 'nome_socio' => $nome, 'cnpj_cpf_do_socio' => $doc,
        'identificador_de_socio' => $identificador, 'qualificacao_do_socio' => '49', 'data_entrada_sociedade' => $entrada,
    ]);
}

/** Aeronave na cópia local do RAB em nome da devedora. */
function devAeronave(string $marca, ?string $gravame = 'NENHUM GRAVAME REGISTRADO'): void
{
    if (! DB::table('aeronave_cargas')->exists()) {
        DB::table('aeronave_cargas')->insert(['data_arquivo' => '2026-11-25', 'aeronaves' => 1, 'carregado_em' => now()]);
    }
    DB::table('aeronaves')->insert(['marca' => $marca, 'modelo' => 'EMB-505', 'fabricante' => 'EMBRAER', 'gravame' => $gravame]);
    DB::table('aeronave_vinculos')->insert(['marca' => $marca, 'papel' => 'proprietario', 'tipo' => 'PJ', 'cnpj' => '11222333000181', 'cnpj_basico' => '11222333', 'nome' => 'PADARIA AURORA LTDA', 'nome_norm' => 'PADARIA AURORA LTDA', 'percentual' => 100]);
}

/** Devedora da carteira (com coleta) e o usuário da organização. */
function devedora(bool $marcar = true): array
{
    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);
    CompanySnapshot::factory()->create(['monitored_company_id' => $company->id, 'razao_social' => 'PADARIA AURORA LTDA']);

    if ($marcar) {
        $company->forceFill(['recuperacao_desde' => now()])->save();
    }

    return [$company->fresh(), User::factory()->for($org)->create()];
}

function varrer(): void
{
    test()->artisan('vigilo:varrer-devedores', ['--all' => true])->assertSuccessful();
}

/** @return list<string> */
function alertas(MonitoredCompany $company): array
{
    return ChangeEvent::query()->where('monitored_company_id', $company->id)->orderBy('id')->pluck('new_value')->all();
}

it('records a silent baseline when the debtor is flagged, then alerts only on what is NEW', function () {
    devBase();
    devAeronave('PRAAA');
    [$company] = devedora();

    (new SincronizarDevedorJob($company->id))->handle(app(DevedorMonitor::class));
    expect(alertas($company))->toBe([])
        ->and(RecuperacaoItem::query()->where('tipo', 'aeronave')->pluck('chave')->all())->toBe(['PRAAA']);

    // Depois da linha de base: aeronave sem ônus, participação e possível sucessora novas.
    devAeronave('PRBBB');
    devEmpresa('44555666', 'INVESTIDA NOVA LTDA', '2026-11-01', '6462000');
    devSocio('44555666', 'PADARIA AURORA LTDA', '11222333', '2026-11-01', identificador: '1');
    devEmpresa('22333444', 'AURORA PAES LTDA', '2026-11-10');
    devSocio('22333444', 'MARIA SOUZA', '***456789**', '2026-11-10');
    varrer();

    $eventos = ChangeEvent::query()->where('monitored_company_id', $company->id)->get()->keyBy('field');
    expect($eventos->keys()->sort()->values()->all())->toBe(['recuperacao_aeronave', 'recuperacao_participacao', 'recuperacao_sucessora'])
        ->and($eventos['recuperacao_aeronave']->type)->toBe(ChangeType::BemNovo)
        ->and($eventos['recuperacao_aeronave']->severity)->toBe(Severity::High) // dela, ativa e sem ônus
        ->and($eventos['recuperacao_aeronave']->new_value)->toContain('PRBBB')->toContain('sem ônus')
        ->and($eventos['recuperacao_participacao']->new_value)->toContain('INVESTIDA NOVA LTDA')
        ->and($eventos['recuperacao_sucessora']->type)->toBe(ChangeType::SucessoraNova)
        ->and($eventos['recuperacao_sucessora']->severity)->toBe(Severity::Medium) // CNAE + sócio = médio
        ->and($eventos['recuperacao_sucessora']->new_value)->toContain('AURORA PAES LTDA')->not->toContain('***');

    // Nada novo: nenhuma repetição.
    varrer();
    expect(alertas($company))->toHaveCount(3)
        ->and(MaintenanceRun::ultima('devedores')?->isSuccess())->toBeTrue();
});

it('baselines each item type the first time its source answers completely', function () {
    devBase();
    [$company] = devedora();

    varrer(); // cópia do RAB nunca carregada: o tipo aeronave fica de fora
    expect($company->fresh()->tiposSincronizados())->not->toContain('aeronave');

    devAeronave('PRAAA'); // a cópia chega com uma aeronave que ela já tinha
    varrer();
    expect(alertas($company))->toBe([])
        ->and($company->fresh()->tiposSincronizados())->toContain('aeronave');

    devAeronave('PRCCC', 'ALIENAÇÃO FIDUCIÁRIA');
    varrer();
    expect(alertas($company))->toHaveCount(1)
        ->and(ChangeEvent::query()->first()?->severity)->toBe(Severity::Medium); // com ônus
});

it('keeps a vanished item for a few rounds (no repeated alert when it comes back)', function () {
    devBase();
    devAeronave('PRAAA');
    [$company] = devedora();
    varrer();

    DB::table('aeronave_vinculos')->where('marca', 'PRAAA')->delete();
    varrer();
    varrer();
    expect(RecuperacaoItem::query()->where('chave', 'PRAAA')->value('ausencias'))->toBe(2);

    DB::table('aeronave_vinculos')->insert(['marca' => 'PRAAA', 'papel' => 'proprietario', 'tipo' => 'PJ', 'cnpj' => '11222333000181', 'cnpj_basico' => '11222333', 'nome' => 'X', 'nome_norm' => 'X']);
    varrer();
    expect(alertas($company))->toBe([])
        ->and(RecuperacaoItem::query()->where('chave', 'PRAAA')->value('ausencias'))->toBe(0);
});

it('never compares a source that failed, keeps what it knew and retries in the next pass', function () {
    devBase();
    devAeronave('PRAAA');
    devEmpresa('44555666', 'INVESTIDA LTDA', '2015-01-01', '6462000');
    devSocio('44555666', 'PADARIA AURORA LTDA', '11222333', '2015-01-01', identificador: '1');
    devEmpresa('22333444', 'AURORA PAES LTDA', '2022-01-01');
    devSocio('22333444', 'MARIA SOUZA', '***456789**', '2022-01-01');
    [$company] = devedora();
    varrer();
    expect(RecuperacaoItem::query()->whereIn('tipo', ['participacao', 'sucessora'])->pluck('chave')->sort()->values()->all())->toBe(['22333444', '44555666']);
    $checadaEm = $company->fresh()->recuperacao_synced_at;

    Schema::connection('cnpj')->drop('socios'); // participações e sucessoras falham
    devAeronave('PRBBB');
    $this->travelTo(now()->addHour());
    varrer();

    expect(alertas($company))->toHaveCount(1) // só a aeronave (fonte inteira)
        ->and(RecuperacaoItem::query()->whereIn('tipo', ['participacao', 'sucessora'])->pluck('ausencias')->all())->toBe([0, 0]) // conhecido intacto
        ->and($company->fresh()->recuperacao_synced_at?->equalTo($checadaEm))->toBeTrue(); // não valeu como checagem do dia: a 2ª passada repete
});

it('the activation job never swallows an alert of a type already swept', function () {
    devBase();
    devAeronave('PRAAA');
    [$company] = devedora();
    varrer(); // linha de base pela varredura

    devAeronave('PRBBB');
    (new SincronizarDevedorJob($company->id))->handle(app(DevedorMonitor::class)); // re-disparado (ex.: salvar a data)
    expect(RecuperacaoItem::query()->where('chave', 'PRBBB')->exists())->toBeFalse();

    varrer();
    expect(alertas($company))->toHaveCount(1)->and(alertas($company)[0])->toContain('PRBBB');
});

it('sweeps only flagged debtors and records the routine on the admin card at 08:00 and 13:00 Brasília', function () {
    devBase();
    devAeronave('PRAAA');
    [$naoMarcada] = devedora(marcar: false);

    varrer();

    expect($naoMarcada->fresh()->recuperacao_synced_at)->toBeNull()
        ->and(RecuperacaoItem::query()->count())->toBe(0);

    $linha = collect(app(StatusRotinas::class)->todas())->firstWhere('nome', 'Monitoramento de devedoras (alertas)');
    expect($linha?->situacao)->toBe('ok');

    $evento = collect(app(Schedule::class)->events())->first(fn ($e) => str_contains((string) $e->command, 'vigilo:varrer-devedores'));
    expect($evento?->expression)->toBe('0 8,13 * * *')->and($evento?->timezone)->toBe('America/Sao_Paulo');
});

it('flags and unflags a debtor from the portfolio company page', function () {
    Queue::fake();
    [$company, $user] = devedora(marcar: false);

    Livewire::actingAs($user)->test(Recuperacao::class, ['company' => $company])
        ->assertSee('Monitorar como devedora')
        ->set('data', '2999-01-01')->call('ativar')->assertHasErrors('data')
        ->set('data', '2021-06-30')->call('ativar')->assertHasNoErrors()
        ->assertSee('Devedora monitorada');

    $company->refresh();
    expect($company->emRecuperacao())->toBeTrue()
        ->and($company->recuperacao_marco?->toDateString())->toBe('2021-06-30');
    Queue::assertPushed(SincronizarDevedorJob::class, fn ($job) => $job->monitoredCompanyId === $company->id);

    RecuperacaoItem::query()->create(['monitored_company_id' => $company->id, 'tipo' => 'aeronave', 'chave' => 'PRAAA', 'visto_em' => now()]);
    Livewire::actingAs($user)->test(Recuperacao::class, ['company' => $company])
        ->call('desativar')
        ->assertSee('Monitorar como devedora');

    expect($company->fresh()->emRecuperacao())->toBeFalse()->and(RecuperacaoItem::query()->count())->toBe(0);
});

it('a new debt date resets only the successor baseline', function () {
    Queue::fake();
    [$company, $user] = devedora();
    $company->forceFill(['recuperacao_tipos' => 'aeronave,sucessora', 'recuperacao_synced_at' => now()])->save();
    RecuperacaoItem::query()->create(['monitored_company_id' => $company->id, 'tipo' => 'sucessora', 'chave' => '22333444', 'visto_em' => now()]);
    RecuperacaoItem::query()->create(['monitored_company_id' => $company->id, 'tipo' => 'aeronave', 'chave' => 'PRAAA', 'visto_em' => now()]);

    Livewire::actingAs($user)->test(Recuperacao::class, ['company' => $company])->set('data', '2018-01-01')->call('ativar');

    $company->refresh();
    expect($company->tiposSincronizados())->toBe(['aeronave'])
        ->and(RecuperacaoItem::query()->pluck('chave')->all())->toBe(['PRAAA']);
});

it('forbids another organization and is mounted on the portfolio page with the debt date', function () {
    [$company, $user] = devedora();
    $company->forceFill(['recuperacao_marco' => '2021-06-30'])->save();

    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(Recuperacao::class, ['company' => $company])->assertForbidden();

    Livewire::actingAs($user)->test(Show::class, ['company' => $company->fresh()])
        ->assertSeeLivewire(Recuperacao::class)
        ->assertSeeLivewire(IndiciosEsvaziamento::class);

    // O card de indícios aceita a data da devedora (validada) como marco.
    Livewire::actingAs($user)->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181', 'marco' => '2021-06-30'])
        ->assertSet('marcoInformado', '2021-06-30');
    Livewire::actingAs($user)->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181', 'marco' => '2999-01-01'])
        ->assertSet('marcoInformado', null);
});

it('opens the circuit breaker when the CNPJ base is down (instead of a green round)', function () {
    devAeronave('PRAAA'); // a cópia do RAB (banco local) segue respondendo
    [$a] = devedora();
    [$b] = devedora();
    config()->set('risco.sweep.max_consecutive_failures', 2);
    // sem useCnpjSqlite: a conexão cnpj recusa (porta 1)

    test()->artisan('vigilo:varrer-devedores')->expectsOutputToContain('varredura abortada')->assertFailed();

    expect(MaintenanceRun::ultima('devedores')?->isSuccess())->toBeFalse()
        ->and($a->fresh()->recuperacao_synced_at)->toBeNull()
        ->and($b->fresh()->recuperacao_synced_at)->toBeNull();
});

it('skips a debtor already checked today unless --all', function () {
    devBase();
    devAeronave('PRAAA');
    [$company] = devedora();
    varrer();

    devAeronave('PRBBB');
    test()->artisan('vigilo:varrer-devedores')->assertSuccessful(); // já checada hoje: pula
    expect(alertas($company))->toBe([]);

    varrer(); // --all
    expect(alertas($company))->toHaveCount(1);
});

it('the activation job does not count as the day check (the sweep still runs today)', function () {
    devBase();
    devAeronave('PRAAA');
    [$company] = devedora();

    (new SincronizarDevedorJob($company->id))->handle(app(DevedorMonitor::class));
    expect($company->fresh()->recuperacao_synced_at)->toBeNull()
        ->and($company->fresh()->tiposSincronizados())->toContain('aeronave');

    devAeronave('PRBBB');
    test()->artisan('vigilo:varrer-devedores')->assertSuccessful(); // sem --all: não foi pulada
    expect(alertas($company))->toHaveCount(1);
});

it('drops the successors of a stale debt date when the date changed between query and apply', function () {
    devBase();
    devEmpresa('22333444', 'AURORA PAES LTDA', '2022-01-01');
    devSocio('22333444', 'MARIA SOUZA', '***456789**', '2022-01-01');
    [$company] = devedora();
    $monitor = app(DevedorMonitor::class);

    $consulta = $monitor->consultar($company); // marco = PGFN (2020)
    $monitor->marcar($company, now()->setDate(2023, 1, 1)->startOfDay()); // o usuário troca a data no meio
    $monitor->aplicar($company, $consulta);

    expect($company->fresh()->tiposSincronizados())->not->toContain('sucessora')
        ->and(RecuperacaoItem::query()->where('tipo', 'sucessora')->exists())->toBeFalse();
});

it('returns nothing for a debtor unflagged in the middle of the sweep', function () {
    devBase();
    devAeronave('PRAAA');
    [$company] = devedora();
    $monitor = app(DevedorMonitor::class);
    $consulta = $monitor->consultar($company);

    $monitor->desmarcar($company);

    expect($monitor->aplicar($company, $consulta))->toBe([])
        ->and(RecuperacaoItem::query()->count())->toBe(0);
});

it('alerts a new MEI/EI successor WITHOUT the CPF in its name (it goes out by e-mail/webhook)', function () {
    devBase();
    [$company] = devedora();
    varrer();

    // Empresário individual aberto depois da dívida, mesmo ramo, sócia em comum: a Receita põe o CPF no nome.
    devEmpresa('55666777', 'MARIA SOUZA 12345678901', '2026-11-01');
    devSocio('55666777', 'MARIA SOUZA', '***456789**', '2026-11-01');
    varrer();

    $alerta = alertas($company)[0] ?? '';
    expect($alerta)->toContain('MARIA SOUZA')
        ->not->toContain('12345678901')
        ->not->toContain('***')
        ->not->toMatch('/\d{6}\*\*/');
});

it('alerts a new federal contract in force (keyed by the Portal id), never an expired one', function () {
    devBase();
    config()->set('risco.portal.api_key', 'chave-teste');
    $contratos = [[
        'id' => 9, 'numero' => '1', 'objeto' => 'Pães', 'dataAssinatura' => '2025-01-01', 'dataInicioVigencia' => '2025-01-01',
        'dataFimVigencia' => '2099-01-01', 'valorInicialCompra' => 5000, 'valorFinalCompra' => 5000, 'fornecedor' => ['cnpjFormatado' => '11.222.333/0001-81'],
    ]];
    Http::fake(['*portaldatransparencia*' => function () use (&$contratos) {
        return Http::response($contratos);
    }]);
    [$company] = devedora();
    varrer(); // linha de base com o contrato 9

    Cache::flush(); // o Portal tem cache de 24 h
    $contratos[] = ['id' => 10] + $contratos[0];
    $contratos[] = ['id' => 11, 'dataFimVigencia' => '2020-01-01'] + $contratos[0]; // vencido: não é crédito a receber
    varrer();

    expect(alertas($company))->toHaveCount(1)
        ->and(alertas($company)[0])->toContain('Contrato federal vigente')->toContain('R$ 5.000,00')
        ->and(RecuperacaoItem::query()->where('tipo', 'contrato')->pluck('chave')->all())->toEqualCanonicalizing(['9', '10']);
});

it('does not compare aircraft when the list hits the ceiling', function () {
    devBase();
    for ($i = 1; $i <= AeronavesLookup::LIMITE; $i++) {
        devAeronave(sprintf('PT%03d', $i));
    }
    [$company] = devedora();

    varrer();

    expect($company->fresh()->tiposSincronizados())->not->toContain('aeronave')
        ->and(RecuperacaoItem::query()->where('tipo', 'aeronave')->exists())->toBeFalse();
});

it('notifies the organization users of the new items', function () {
    Notification::fake();
    devBase();
    devAeronave('PRAAA');
    [$company, $user] = devedora();
    varrer();

    devAeronave('PRBBB');
    varrer();

    Notification::assertSentTo($user, CompanyChangeDetected::class);
});

it('treats the cadastral history being down as a failed successor source (no forgetting, no false "new")', function () {
    devBase();
    devEmpresa('22333444', 'LIMA PAES LTDA', '2024-01-01');
    devSocio('22333444', 'JOAO LIMA', '***111222**', '2024-01-01');
    DB::connection('cnpj')->table('historico_execucoes')->insert(['referencia' => '2026-10', 'status' => 'ok']);
    seedHistorico('11222333', 'socio_saida', json_encode(['nome' => 'JOAO LIMA', 'documento' => '***111222**', 'qualificacao' => '49']), null, '2023-06-01 06:00:00');
    [$company] = devedora();
    varrer();
    expect(RecuperacaoItem::query()->where('tipo', 'sucessora')->pluck('chave')->all())->toBe(['22333444']); // achada pelo ex-sócio

    Schema::connection('cnpj')->drop('historico_cadastral');
    $checadaEm = $company->fresh()->recuperacao_synced_at;
    $this->travelTo(now()->addHour());
    varrer();
    varrer();
    varrer();

    expect(RecuperacaoItem::query()->where('tipo', 'sucessora')->value('ausencias'))->toBe(0)
        ->and($company->fresh()->recuperacao_synced_at?->equalTo($checadaEm))->toBeTrue()
        ->and(MaintenanceRun::ultima('devedores')?->message)->toContain('1 com fonte indisponível');
});

it('a debtor not yet in the CNPJ base is "not applicable", not a failure', function () {
    devBase();
    [$company] = devedora();
    $company->forceFill(['cnpj' => '99888777000155', 'recuperacao_marco' => '2021-01-01'])->save();

    varrer();

    expect($company->fresh()->recuperacao_synced_at)->not->toBeNull()
        ->and($company->fresh()->tiposSincronizados())->not->toContain('sucessora')
        ->and(MaintenanceRun::ultima('devedores')?->message)->toContain('0 com fonte indisponível');
});

it('a strong new successor (address + CNAE + partner) is a HIGH alert', function () {
    devBase();
    [$company] = devedora();
    varrer();

    devEmpresa('22333444', 'AURORA PAES LTDA', '2026-11-10');
    DB::connection('cnpj')->table('estabelecimentos')->where('cnpj_basico', '22333444')->update(['cep' => '01122233', 'numero' => '1']); // o endereço da devedora (devEmpresa usa 0 + 7 díg da raiz)
    devSocio('22333444', 'MARIA SOUZA', '***456789**', '2026-11-10');
    varrer();

    $evento = ChangeEvent::query()->where('field', 'recuperacao_sucessora')->first();
    expect($evento?->severity)->toBe(Severity::High)
        ->and($evento?->new_value)->toContain('mesmo endereço');
});
