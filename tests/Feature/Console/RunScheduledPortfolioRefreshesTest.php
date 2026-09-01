<?php

declare(strict_types=1);

use App\Jobs\RefreshMonitoredCompanyJob;
use App\Models\Portfolio;
use App\Models\PortfolioScheduledRun;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;

it('dispatches the schedules due today and records them', function () {
    Queue::fake();
    $this->travelTo(Carbon::parse('2026-03-15 03:00:00'));

    $portfolio = Portfolio::factory()->create(['schedule_days' => [15]]);
    $portfolio->monitoredCompanies()->create(['cnpj' => '11222333000181']);

    $this->artisan('portfolios:run-scheduled')->assertExitCode(0);

    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 1);
    expect(PortfolioScheduledRun::where('portfolio_id', $portfolio->id)->count())->toBe(1);
});
