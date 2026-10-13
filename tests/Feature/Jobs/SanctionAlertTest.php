<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Jobs\RefreshMonitoredCompanyJob;
use App\Jobs\SendWebhookNotification;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Notifications\CompanyChangeDetected;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

/*
 * O alerta de sanção nova (CGU) vem da VARREDURA DIÁRIA `vigilo:varrer-sancoes`,
 * não do refresh da Receita — o refresh não chama mais a CGU.
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

/** Fakes: Receita (ATIVA, estável) + CGU com uma sanção que SÓ aparece na 2ª varredura. */
function fakeReceitaAndNewSanction(): void
{
    Http::fake([
        'https://brasilapi.com.br/*' => Http::response(sanctionReceitaPayload('ATIVA'), 200),
        '*portaldatransparencia*ceis*' => Http::sequence()
            ->push([], 200) // 1ª varredura (baseline): nada consta
            ->push([[       // 2ª varredura: sanção nova
                'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
                'tipoSancao' => ['descricaoResumida' => 'Empresa inidônea'],
                'orgaoSancionador' => ['nome' => 'TCU'],
                'dataInicioSancao' => '01/03/2024',
            ]], 200),
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
        ->and($company->sanctions()->count())->toBe(0);

    Cache::flush(); // o relatório de risco é cacheado 12h

    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful(); // sanção nova

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
    Cache::flush();
    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful(); // sanção nova

    Queue::assertPushed(SendWebhookNotification::class, function (SendWebhookNotification $job): bool {
        $mudancas = $job->payload['mudancas'] ?? [];

        return is_array($mudancas)
            && collect($mudancas)->contains(fn (array $m): bool => ($m['tipo'] ?? null) === 'sancao_adicionada');
    });
});

it('does not alert for sanctions that already existed at baseline', function () {
    Notification::fake();
    config()->set('risco.portal.api_key', 'chave-teste');

    // CGU devolve a MESMA sanção nas duas varreduras → já existia no baseline.
    Http::fake([
        'https://brasilapi.com.br/*' => Http::response(sanctionReceitaPayload('ATIVA'), 200),
        '*portaldatransparencia*ceis*' => Http::response([[
            'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
            'tipoSancao' => ['descricaoResumida' => 'Empresa inidônea'],
            'dataInicioSancao' => '01/03/2020',
        ]], 200),
        '*portaldatransparencia*' => Http::response([], 200),
    ]);

    $company = sanctionJobCompany();
    RefreshMonitoredCompanyJob::dispatchSync($company);

    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful(); // baseline: já consta a sanção
    Cache::flush();
    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful(); // mesma sanção → sem alerta

    expect($company->changeEvents()->where('type', ChangeType::SancaoAdicionada->value)->count())->toBe(0)
        ->and($company->sanctions()->count())->toBe(1);

    Notification::assertNothingSent();
});

it('skips companies with no collection yet (no snapshot to attach the event)', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake();

    $company = sanctionJobCompany(); // nunca coletada → sem snapshot

    $this->artisan('vigilo:varrer-sancoes')
        ->expectsOutputToContain('sem coleta ainda')
        ->assertSuccessful();

    Http::assertNothingSent(); // nem chama a CGU por ela
    expect($company->fresh()->sanctions_synced_at)->toBeNull();
});

it('trips the circuit breaker and aborts when the CGU is down', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    config()->set('risco.sweep.max_consecutive_failures', 2);
    Http::fake(['*portaldatransparencia*' => Http::response('erro', 500)]);

    $org = Organization::factory()->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    foreach (['11222333000181', '44555666000199', '77888999000155'] as $cnpj) {
        $company = MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => $cnpj]);
        CompanySnapshot::factory()->create(['monitored_company_id' => $company->id]);
    }

    $this->artisan('vigilo:varrer-sancoes')
        ->expectsOutputToContain('varredura abortada')
        ->assertFailed();

    // Nenhuma foi dada como checada — e a 3ª nem foi consultada (abortou na 2ª).
    expect(MonitoredCompany::query()->whereNotNull('sanctions_synced_at')->count())->toBe(0)
        ->and(MonitoredCompany::query()->where('cnpj', '77888999000155')->first()->sanctions()->count())->toBe(0);
    Http::assertNotSent(fn (Request $request): bool => str_contains($request->url(), '77888999000155'));
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
