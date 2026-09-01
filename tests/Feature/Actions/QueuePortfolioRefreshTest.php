<?php

declare(strict_types=1);

use App\Actions\QueuePortfolioRefresh;
use App\Enums\RefreshStatus;
use App\Jobs\RefreshMonitoredCompanyJob;
use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Queue;

it('resets status to pending and queues a refresh for every company', function () {
    Queue::fake();
    $portfolio = Portfolio::factory()->create();
    $portfolio->monitoredCompanies()->create(['cnpj' => '11222333000181', 'last_refresh_status' => RefreshStatus::Ok]);
    $portfolio->monitoredCompanies()->create(['cnpj' => '11444777000161', 'last_refresh_status' => RefreshStatus::Error, 'last_refresh_error' => 'boom']);

    $count = app(QueuePortfolioRefresh::class)->handle($portfolio);

    expect($count)->toBe(2);
    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 2);

    $portfolio->monitoredCompanies->each(function ($company): void {
        expect($company->fresh()->last_refresh_status)->toBeNull()
            ->and($company->fresh()->last_refresh_error)->toBeNull();
    });
});

it('honours an optional constraint and leaves other companies untouched', function () {
    Queue::fake();
    $portfolio = Portfolio::factory()->create();
    $ok = $portfolio->monitoredCompanies()->create(['cnpj' => '11222333000181', 'last_refresh_status' => RefreshStatus::Ok]);
    $errored = $portfolio->monitoredCompanies()->create(['cnpj' => '11444777000161', 'last_refresh_status' => RefreshStatus::Error]);

    $count = app(QueuePortfolioRefresh::class)->handle($portfolio, function (Builder $query): void {
        $query->where(fn (Builder $inner) => $inner
            ->whereNull('last_refresh_status')
            ->orWhere('last_refresh_status', RefreshStatus::Error->value));
    });

    expect($count)->toBe(1);
    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 1);
    expect($ok->fresh()->last_refresh_status)->toBe(RefreshStatus::Ok) // intocado
        ->and($errored->fresh()->last_refresh_status)->toBeNull();
});

it('never touches companies from another portfolio (constraint stays scoped)', function () {
    Queue::fake();
    $a = Portfolio::factory()->create();
    $b = Portfolio::factory()->create();
    $aErr = $a->monitoredCompanies()->create(['cnpj' => '11222333000181', 'last_refresh_status' => RefreshStatus::Error]);
    $bErr = $b->monitoredCompanies()->create(['cnpj' => '11444777000161', 'last_refresh_status' => RefreshStatus::Error]);

    $count = app(QueuePortfolioRefresh::class)->handle($a, function (Builder $query): void {
        $query->where(fn (Builder $inner) => $inner
            ->whereNull('last_refresh_status')
            ->orWhere('last_refresh_status', RefreshStatus::Error->value));
    });

    expect($count)->toBe(1); // só a empresa do portfólio A
    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 1);
    expect($aErr->fresh()->last_refresh_status)->toBeNull()
        ->and($bErr->fresh()->last_refresh_status)->toBe(RefreshStatus::Error); // portfólio B intocado
});
