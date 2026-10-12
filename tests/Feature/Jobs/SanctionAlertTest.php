<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Jobs\RefreshMonitoredCompanyJob;
use App\Jobs\SendWebhookNotification;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Notifications\CompanyChangeDetected;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

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

/** Fakes: Receita (ATIVA, estável) + CGU com uma sanção que SÓ aparece na 2ª coleta. */
function fakeReceitaAndNewSanction(): void
{
    Http::fake([
        'https://brasilapi.com.br/*' => Http::response(sanctionReceitaPayload('ATIVA'), 200),
        '*portaldatransparencia*ceis*' => Http::sequence()
            ->push([], 200) // baseline: nada consta
            ->push([[       // depois: sanção nova
                'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
                'tipoSancao' => ['descricaoResumida' => 'Empresa inidônea'],
                'orgaoSancionador' => ['nome' => 'TCU'],
                'dataInicioSancao' => '01/03/2024',
            ]], 200),
        '*portaldatransparencia*' => Http::response([], 200), // cnep/cepim
    ]);
}

it('raises a critical alert and notifies when a new sanction appears', function () {
    Notification::fake();
    config()->set('risco.portal.api_key', 'chave-teste');
    fakeReceitaAndNewSanction();

    $company = sanctionJobCompany();

    RefreshMonitoredCompanyJob::dispatchSync($company); // baseline: ATIVA, sem sanção
    expect($company->changeEvents()->count())->toBe(0)
        ->and($company->sanctions()->count())->toBe(0);

    Cache::flush(); // o relatório de risco é cacheado 12h

    RefreshMonitoredCompanyJob::dispatchSync($company); // sanção nova

    $event = $company->changeEvents()->where('type', ChangeType::SancaoAdicionada->value)->first();

    expect($event)->not->toBeNull()
        ->and($event->severity)->toBe(Severity::Critical)
        ->and($event->new_value)->toContain('CEIS')
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

    RefreshMonitoredCompanyJob::dispatchSync($company); // baseline
    Cache::flush();
    RefreshMonitoredCompanyJob::dispatchSync($company); // sanção nova

    Queue::assertPushed(SendWebhookNotification::class, function (SendWebhookNotification $job): bool {
        $mudancas = $job->payload['mudancas'] ?? [];

        return is_array($mudancas)
            && collect($mudancas)->contains(fn (array $m): bool => ($m['tipo'] ?? null) === 'sancao_adicionada');
    });
});

it('does not alert for sanctions that already existed at baseline', function () {
    Notification::fake();
    config()->set('risco.portal.api_key', 'chave-teste');

    // CGU devolve a MESMA sanção nas duas coletas → já existia no baseline.
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

    RefreshMonitoredCompanyJob::dispatchSync($company); // baseline: já consta a sanção
    Cache::flush();
    RefreshMonitoredCompanyJob::dispatchSync($company); // mesma sanção → sem alerta

    expect($company->changeEvents()->where('type', ChangeType::SancaoAdicionada->value)->count())->toBe(0)
        ->and($company->sanctions()->count())->toBe(1);

    Notification::assertNothingSent();
});
