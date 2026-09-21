<?php

declare(strict_types=1);

use App\Jobs\RefreshMonitoredCompanyJob;
use App\Models\MonitoredCompany;
use Illuminate\Support\Facades\Queue;

it('queues a refresh for every monitored company across organizations', function () {
    Queue::fake();

    MonitoredCompany::factory()->count(3)->create(); // cada uma cria seu portfólio/org

    $this->artisan('vigilo:recoletar-carteira')->assertSuccessful();

    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 3);
    Queue::assertPushed(RefreshMonitoredCompanyJob::class, fn (RefreshMonitoredCompanyJob $job): bool => $job->rebaseline === false);
});

it('passes the rebaseline flag to the queued jobs', function () {
    Queue::fake();

    MonitoredCompany::factory()->create();

    $this->artisan('vigilo:recoletar-carteira', ['--rebaseline' => true])->assertSuccessful();

    Queue::assertPushed(RefreshMonitoredCompanyJob::class, fn (RefreshMonitoredCompanyJob $job): bool => $job->rebaseline === true);
});

it('reports zero when there are no companies', function () {
    Queue::fake();

    $this->artisan('vigilo:recoletar-carteira')
        ->expectsOutputToContain('0 empresa(s)')
        ->assertSuccessful();

    Queue::assertNothingPushed();
});
