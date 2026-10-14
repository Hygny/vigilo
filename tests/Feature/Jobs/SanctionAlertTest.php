<?php

declare(strict_types=1);

use App\DTO\Risco\RiscoReport;
use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Jobs\RefreshMonitoredCompanyJob;
use App\Jobs\SendWebhookNotification;
use App\Models\ChangeEvent;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Notifications\CompanyChangeDetected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

/*
 * O alerta de sanção nova (CGU) vem da VARREDURA DIÁRIA `vigilo:varrer-sancoes`,
 * não do refresh da Receita — o refresh não chama mais a CGU.
 *
 * Sem `Cache::flush()` entre as varreduras de propósito: a 1ª deixa o relatório
 * no cache (12h) e a 2ª tem de ler a CGU de novo mesmo assim.
 */

beforeEach(function (): void {
    config()->set('risco.sweep.per_minute', 0); // sem pausa entre empresas nos testes
});

/**
 * @return array<string, mixed>
 */
function sanctionReceitaPayload(string $situacao = 'ATIVA'): array
{
    return [
        'cnpj' => '11222333000181',
        'razao_social' => 'EMPRESA EXEMPLO LTDA',
        'nome_fantasia' => 'EXEMPLO',
        'descricao_situacao_cadastral' => $situacao,
        'data_situacao_cadastral' => '2005-11-03',
        'cnae_fiscal' => 6201501,
        'porte' => 'DEMAIS',
        'natureza_juridica' => '206-2 - Sociedade Empresária Limitada',
        'logradouro' => 'RUA DAS FLORES',
        'numero' => '100',
        'municipio' => 'SAO PAULO',
        'uf' => 'SP',
        'qsa' => [
            ['nome_socio' => 'FULANO DE TAL', 'cnpj_cpf_do_socio' => '***123456**', 'qualificacao_socio' => 'Sócio-Administrador'],
        ],
    ];
}

function sanctionJobCompany(): MonitoredCompany
{
    $org = Organization::factory()->create();
    User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    return MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '11222333000181']);
}

/**
 * Empresas já coletadas (com snapshot), na ordem dos CNPJs dados.
 *
 * @param  list<string>  $cnpjs
 * @return list<MonitoredCompany>
 */
function sweepCompanies(array $cnpjs): array
{
    $org = Organization::factory()->create();
    User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    return array_map(function (string $cnpj) use ($portfolio): MonitoredCompany {
        $company = MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => $cnpj]);
        CompanySnapshot::factory()->create(['monitored_company_id' => $company->id]);

        return $company;
    }, $cnpjs);
}

/**
 * @return array<string, mixed>
 */
function ceisRecord(string $cnpjFormatado = '11.222.333/0001-81'): array
{
    return [
        'pessoa' => ['cnpjFormatado' => $cnpjFormatado],
        'tipoSancao' => ['descricaoResumida' => 'Empresa inidônea'],
        'orgaoSancionador' => ['nome' => 'TCU'],
        'dataInicioSancao' => '01/03/2024',
    ];
}

/** Fakes: Receita (ATIVA, estável) + CGU com uma sanção que SÓ aparece na 2ª varredura. */
function fakeReceitaAndNewSanction(): void
{
    Http::fake([
        'https://brasilapi.com.br/*' => Http::response(sanctionReceitaPayload('ATIVA'), 200),
        '*portaldatransparencia*ceis*' => Http::sequence()
            ->push([], 200)              // 1ª varredura (baseline): nada consta
            ->push([ceisRecord()], 200), // 2ª varredura: sanção nova
        '*portaldatransparencia*' => Http::response([], 200), // cnep/cepim
    ]);
}

it('the Receita refresh no longer calls the CGU', function () {
    Notification::fake();
    config()->set('risco.portal.api_key', 'chave-teste');
    fakeReceitaAndNewSanction();

    $company = sanctionJobCompany();
    RefreshMonitoredCompanyJob::dispatchSync($company);

    Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), 'portaldatransparencia'));
    expect($company->latestSnapshot()->exists())->toBeTrue()            // a coleta da Receita aconteceu
        ->and($company->fresh()->sanctions_synced_at)->toBeNull();       // mas sanção não foi tocada
});

it('raises a critical alert and notifies when the sweep finds a new sanction', function () {
    Notification::fake();
    config()->set('risco.portal.api_key', 'chave-teste');
    fakeReceitaAndNewSanction();

    $company = sanctionJobCompany();
    RefreshMonitoredCompanyJob::dispatchSync($company); // coleta (snapshot) — sem CGU

    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful(); // baseline: nada consta
    expect($company->changeEvents()->count())->toBe(0)
        ->and($company->sanctions()->count())->toBe(0)
        ->and(Cache::get('risco:portal:11222333000181'))->toBeInstanceOf(RiscoReport::class); // ficou no cache

    // 3h depois o relatório "nada consta" ainda vale no cache (12h) — a varredura
    // tem de ler a CGU de novo mesmo assim (--all: a empresa já foi checada hoje).
    $this->travel(3)->hours();
    expect(Cache::get('risco:portal:11222333000181'))->toBeInstanceOf(RiscoReport::class);
    $this->artisan('vigilo:varrer-sancoes', ['--all' => true])->assertSuccessful(); // sanção nova

    $event = $company->changeEvents()->where('type', ChangeType::SancaoAdicionada->value)->first();

    expect($event)->not->toBeNull()
        ->and($event->severity)->toBe(Severity::Critical)
        ->and($event->new_value)->toContain('CEIS')
        ->and($event->to_snapshot_id)->toBe($company->latestSnapshot->id) // ligado à coleta mais recente
        ->and($company->sanctions()->count())->toBe(1);

    Notification::assertSentTo(
        $company->portfolio->organization->users->first(),
        CompanyChangeDetected::class
    );
});

it('includes the new sanction in the outbound webhook', function () {
    Notification::fake();
    Queue::fake([SendWebhookNotification::class]);
    config()->set('risco.portal.api_key', 'chave-teste');
    fakeReceitaAndNewSanction();

    $company = sanctionJobCompany();
    $company->portfolio->organization->forceFill([
        'webhook_url' => 'https://consumidor.test/hook',
        'webhook_secret' => 'segredo-super-secreto-123',
    ])->save();

    RefreshMonitoredCompanyJob::dispatchSync($company);
    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful(); // baseline
    $this->travel(1)->days();
    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful(); // sanção nova

    Queue::assertPushed(SendWebhookNotification::class, function (SendWebhookNotification $job): bool {
        $mudancas = $job->payload['mudancas'] ?? [];

        return is_array($mudancas)
            && collect($mudancas)->contains(fn (array $m): bool => ($m['tipo'] ?? null) === 'sancao_adicionada');
    });
});

it('queues the alert notification so a mail outage never stops the sweep', function () {
    expect(new CompanyChangeDetected(new MonitoredCompany, 'X', new Collection))
        ->toBeInstanceOf(ShouldQueue::class);
});

it('does not alert for sanctions that already existed at baseline', function () {
    Notification::fake();
    config()->set('risco.portal.api_key', 'chave-teste');

    // CGU devolve a MESMA sanção nas duas varreduras → já existia no baseline.
    Http::fake([
        'https://brasilapi.com.br/*' => Http::response(sanctionReceitaPayload('ATIVA'), 200),
        '*portaldatransparencia*ceis*' => Http::response([ceisRecord()], 200),
        '*portaldatransparencia*' => Http::response([], 200),
    ]);

    $company = sanctionJobCompany();
    RefreshMonitoredCompanyJob::dispatchSync($company);

    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful(); // baseline: já consta a sanção
    $this->travel(1)->days();
    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful(); // mesma sanção → sem alerta

    expect($company->changeEvents()->where('type', ChangeType::SancaoAdicionada->value)->count())->toBe(0)
        ->and($company->sanctions()->count())->toBe(1);

    Notification::assertNothingSent();
});

it('rolls back one company on an unexpected error, keeps sweeping, and re-alerts it on the next pass', function () {
    Notification::fake();
    config()->set('risco.portal.api_key', 'chave-teste');
    [$a, $b] = sweepCompanies(['11222333000181', '44555666000199']);

    $sancionadas = false;
    Http::fake(function (Request $request) use (&$sancionadas) {
        return Http::response(match (true) {
            ! $sancionadas, ! str_contains($request->url(), '/ceis') => [],
            str_contains($request->url(), '11222333000181') => [ceisRecord('11.222.333/0001-81')],
            default => [ceisRecord('44.555.666/0001-99')],
        });
    });

    // Baseline das duas (nada consta)…
    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful();

    // …no dia seguinte as duas aparecem sancionadas, mas gravar o evento de A falha.
    $this->travel(1)->days();
    $sancionadas = true;
    ChangeEvent::creating(function (ChangeEvent $event) use ($a): void {
        if ($event->monitored_company_id === $a->id) {
            throw new RuntimeException('falha ao gravar o evento');
        }
    });

    $this->artisan('vigilo:varrer-sancoes')
        ->expectsOutputToContain('1 com erro')
        ->assertSuccessful();

    // A: tudo desfeito (sanção não ficou "conhecida" sem o alerta); B: alertada normalmente.
    expect($a->sanctions()->count())->toBe(0)
        ->and($a->changeEvents()->count())->toBe(0)
        ->and($a->fresh()->sanctions_synced_at->lt(today()))->toBeTrue()
        ->and($b->changeEvents()->where('type', ChangeType::SancaoAdicionada->value)->count())->toBe(1);

    // Falha resolvida: a 2ª passada do dia retoma só A — e o alerta não se perdeu.
    ChangeEvent::flushEventListeners();
    $this->artisan('vigilo:varrer-sancoes')
        ->expectsOutputToContain('1 empresa(s) checada(s), 1 alerta(s)')
        ->assertSuccessful();

    expect($a->changeEvents()->where('type', ChangeType::SancaoAdicionada->value)->count())->toBe(1)
        ->and($b->changeEvents()->count())->toBe(1); // B não foi re-alertada
});

it('skips companies already checked today unless --all', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(['*portaldatransparencia*' => Http::response([], 200)]);
    [$hoje, $ontem] = sweepCompanies(['11222333000181', '44555666000199']);
    $hoje->forceFill(['sanctions_synced_at' => now()])->save();
    $ontem->forceFill(['sanctions_synced_at' => now()->subDay()])->save();

    $this->artisan('vigilo:varrer-sancoes')
        ->expectsOutputToContain('1 empresa(s) checada(s)')
        ->assertSuccessful();

    Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '11222333000181'));
    Http::assertSent(fn (Request $request): bool => str_contains($request->url(), '44555666000199'));

    $this->artisan('vigilo:varrer-sancoes', ['--all' => true])
        ->expectsOutputToContain('2 empresa(s) checada(s)')
        ->assertSuccessful();
});

it('skips companies with no collection yet (no snapshot to attach the event)', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake();

    $company = sanctionJobCompany(); // nunca coletada → sem snapshot

    $this->artisan('vigilo:varrer-sancoes')
        ->expectsOutputToContain('1 sem coleta ainda')
        ->assertSuccessful();

    Http::assertNothingSent(); // nem chama a CGU por ela
    expect($company->fresh()->sanctions_synced_at)->toBeNull();
});

it('trips the circuit breaker and aborts when the CGU is down', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    config()->set('risco.sweep.max_consecutive_failures', 2);
    Http::fake(['*portaldatransparencia*' => Http::response('erro', 500)]);

    sweepCompanies(['11222333000181', '44555666000199', '77888999000155']);

    $this->artisan('vigilo:varrer-sancoes')
        ->expectsOutputToContain('varredura abortada')
        ->assertFailed();

    // Nenhuma foi dada como checada — e a 3ª nem foi consultada (abortou na 2ª).
    expect(MonitoredCompany::query()->whereNotNull('sanctions_synced_at')->count())->toBe(0)
        ->and(MonitoredCompany::query()->where('cnpj', '77888999000155')->first()->sanctions()->count())->toBe(0);
    Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '77888999000155'));
});

it('resets the breaker after a success (only CONSECUTIVE failures abort)', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    config()->set('risco.sweep.max_consecutive_failures', 2);
    // fora, ok, fora, ok → nunca 2 seguidas
    Http::fake(fn (Request $request) => str_contains($request->url(), '11222333000181') || str_contains($request->url(), '77888999000155')
        ? Http::response('erro', 500)
        : Http::response([], 200));

    sweepCompanies(['11222333000181', '44555666000199', '77888999000155', '22333444000166']);

    $this->artisan('vigilo:varrer-sancoes')
        ->expectsOutputToContain('2 empresa(s) checada(s), 0 alerta(s) de sanção nova, 0 sem coleta ainda (puladas), 2 com a CGU indisponível')
        ->assertSuccessful();
});

it('counts unexpected errors toward the breaker (systemic failure aborts)', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    config()->set('risco.sweep.max_consecutive_failures', 2);
    Http::fake(['*portaldatransparencia*' => Http::response([], 200)]);
    sweepCompanies(['11222333000181', '44555666000199', '77888999000155']);

    // Ex.: banco recusando escrita — toda empresa falha ao gravar.
    MonitoredCompany::saving(fn (): never => throw new RuntimeException('db fora'));

    $this->artisan('vigilo:varrer-sancoes')
        ->expectsOutputToContain('varredura abortada')
        ->assertFailed();

    Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '77888999000155'));
});

it('rejects a non-numeric --per-minute instead of silently disabling the pace', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake();

    $this->artisan('vigilo:varrer-sancoes', ['--per-minute' => 'abc'])
        ->expectsOutputToContain('--per-minute')
        ->assertExitCode(2);

    Http::assertNothingSent();
});

it('is a no-op when the CGU integration is not configured', function () {
    config()->set('risco.portal.api_key', null);
    Http::fake();

    $company = sanctionJobCompany();
    CompanySnapshot::factory()->create(['monitored_company_id' => $company->id]);

    $this->artisan('vigilo:varrer-sancoes')
        ->expectsOutputToContain('não configurada')
        ->assertSuccessful();

    Http::assertNothingSent();
});
