<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\RefreshStatus;
use App\Enums\Severity;
use App\Jobs\RefreshMonitoredCompanyJob;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Notifications\CompanyChangeDetected;
use Illuminate\Bus\UniqueLock;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

/**
 * @return array<string, mixed>
 */
function companyPayload(string $situacao = 'ATIVA'): array
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

function tenantCompany(string $cnpj = '11222333000181'): MonitoredCompany
{
    $org = Organization::factory()->create();
    User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    return MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => $cnpj]);
}

it('creates a baseline snapshot without events on the first refresh', function () {
    Notification::fake();
    Http::fake(['https://brasilapi.com.br/*' => Http::response(companyPayload(), 200)]);

    $company = tenantCompany();

    RefreshMonitoredCompanyJob::dispatchSync($company);

    expect($company->snapshots()->count())->toBe(1)
        ->and($company->snapshots()->first()->partners()->count())->toBe(1)
        ->and($company->changeEvents()->count())->toBe(0)
        ->and($company->fresh()->last_refreshed_at)->not->toBeNull()
        ->and($company->fresh()->last_refresh_status)->toBe(RefreshStatus::Ok);

    Notification::assertNothingSent();
});

it('detects changes on a later refresh and notifies the organization', function () {
    Notification::fake();

    // Successive requests return ATIVA then BAIXADA. A single fake with a
    // sequence is required: stacking two Http::fake() calls on the same URL
    // pattern keeps the first stub winning for every request.
    Http::fake(['https://brasilapi.com.br/*' => Http::sequence()
        ->push(companyPayload('ATIVA'), 200)
        ->push(companyPayload('BAIXADA'), 200),
    ]);

    $company = tenantCompany();

    RefreshMonitoredCompanyJob::dispatchSync($company); // baseline (ATIVA)
    RefreshMonitoredCompanyJob::dispatchSync($company); // change (BAIXADA)

    expect($company->snapshots()->count())->toBe(2)
        ->and($company->changeEvents()->count())->toBe(1);

    $event = $company->changeEvents()->first();

    expect($event->type)->toBe(ChangeType::SituacaoChanged)
        ->and($event->severity)->toBe(Severity::Critical)
        ->and($event->from_snapshot_id)->not->toBeNull()
        ->and($event->to_snapshot_id)->not->toBeNull();

    Notification::assertSentTo(
        $company->portfolio->organization->users->first(),
        CompanyChangeDetected::class
    );
});

it('records the attempt but stores nothing when the company is not found', function () {
    Notification::fake();
    Http::fake(['https://brasilapi.com.br/*' => Http::response(['message' => 'not found'], 404)]);

    $company = tenantCompany();

    RefreshMonitoredCompanyJob::dispatchSync($company);

    expect($company->snapshots()->count())->toBe(0)
        ->and($company->changeEvents()->count())->toBe(0)
        ->and($company->fresh()->last_refreshed_at)->not->toBeNull()
        ->and($company->fresh()->last_refresh_status)->toBe(RefreshStatus::NotFound);

    Notification::assertNothingSent();
});

it('is guarded by overlap and rate-limit middleware', function () {
    $company = tenantCompany();

    $middleware = (new RefreshMonitoredCompanyJob($company))->middleware();

    expect($middleware)->toHaveCount(2)
        ->and($middleware[0])->toBeInstanceOf(WithoutOverlapping::class)
        ->and($middleware[1])->toBeInstanceOf(RateLimited::class);
});

it('records an error status when the job ultimately fails', function () {
    $company = tenantCompany();

    (new RefreshMonitoredCompanyJob($company))->failed(new RuntimeException('cURL error 60: SSL problem'));

    $fresh = $company->fresh();

    expect($fresh->last_refresh_status)->toBe(RefreshStatus::Error)
        ->and($fresh->last_refresh_error)->toContain('SSL problem')
        ->and($fresh->last_refreshed_at)->not->toBeNull();
});

it('bounds retries by time rather than a low attempt cap', function () {
    $company = tenantCompany();
    $job = new RefreshMonitoredCompanyJob($company);

    // No low $tries cap (which rate-limit releases would exhaust); time-bounded instead.
    expect($job->retryUntil())->toBeInstanceOf(DateTimeInterface::class)
        ->and($job->retryUntil()->getTimestamp())->toBeGreaterThan(now()->addHours(1)->getTimestamp());
});

it('is unique per company to prevent duplicate enqueues', function () {
    $company = MonitoredCompany::factory()->create();
    $job = new RefreshMonitoredCompanyJob($company);

    expect($job)->toBeInstanceOf(ShouldBeUnique::class)
        ->and($job->uniqueId())->toBe((string) $company->id);
});

it('does not enqueue a duplicate refresh while one is already pending', function () {
    Queue::fake();
    $company = MonitoredCompany::factory()->create();

    RefreshMonitoredCompanyJob::dispatch($company);
    RefreshMonitoredCompanyJob::dispatch($company); // clique repetido em "Atualizar"

    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 1);
});

it('still enqueues distinct companies independently', function () {
    Queue::fake();
    $a = MonitoredCompany::factory()->create();
    $b = MonitoredCompany::factory()->create();

    RefreshMonitoredCompanyJob::dispatch($a);
    RefreshMonitoredCompanyJob::dispatch($b);

    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 2);
});

it('accepts a fresh refresh once the previous one has finished (lock released)', function () {
    Queue::fake();
    $company = MonitoredCompany::factory()->create();

    RefreshMonitoredCompanyJob::dispatch($company);
    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 1);

    // Simulate the job completing: CallQueuedHandler releases the unique lock
    // after handle(), which must then allow the company to be queued again.
    (new UniqueLock(app(CacheRepository::class)))->release(new RefreshMonitoredCompanyJob($company));

    RefreshMonitoredCompanyJob::dispatch($company);
    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 2);
});
