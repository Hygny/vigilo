<?php

declare(strict_types=1);

use App\Actions\RunDuePortfolioSchedules;
use App\Jobs\RefreshMonitoredCompanyJob;
use App\Models\Portfolio;
use App\Models\PortfolioScheduledRun;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Queue;

it('runs portfolios scheduled for the given day and records the run', function () {
    Queue::fake();
    $date = CarbonImmutable::parse('2026-03-15');

    $due = Portfolio::factory()->create(['schedule_days' => [15]]);
    $due->monitoredCompanies()->createMany([['cnpj' => '11222333000181'], ['cnpj' => '11444777000161']]);

    $notDue = Portfolio::factory()->create(['schedule_days' => [10]]);
    $notDue->monitoredCompanies()->create(['cnpj' => '01234567800043']);

    $executed = app(RunDuePortfolioSchedules::class)->handle($date);

    expect($executed)->toBe(1);
    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 2); // só as do portfólio devido

    $run = PortfolioScheduledRun::where('portfolio_id', $due->id)->first();
    expect($run)->not->toBeNull()
        ->and($run->scheduled_day)->toBe(15)
        ->and($run->ran_on->toDateString())->toBe('2026-03-15')
        ->and($run->companies_count)->toBe(2)
        ->and($run->dispatched_count)->toBe(2)
        ->and(PortfolioScheduledRun::where('portfolio_id', $notDue->id)->exists())->toBeFalse();
});

it('slides an out-of-range day to the last day of the month', function () {
    Queue::fake();
    // Fevereiro/2026 tem 28 dias; dia 31 agendado roda em 28/02.
    $p = Portfolio::factory()->create(['schedule_days' => [31]]);
    $p->monitoredCompanies()->create(['cnpj' => '11222333000181']);

    $executed = app(RunDuePortfolioSchedules::class)->handle(CarbonImmutable::parse('2026-02-28'));

    expect($executed)->toBe(1);
    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 1);
    expect(PortfolioScheduledRun::first()->scheduled_day)->toBe(31);
});

it('does not run an out-of-range day before the last day of the month', function () {
    Queue::fake();
    $p = Portfolio::factory()->create(['schedule_days' => [31]]);
    $p->monitoredCompanies()->create(['cnpj' => '11222333000181']);

    $executed = app(RunDuePortfolioSchedules::class)->handle(CarbonImmutable::parse('2026-02-27'));

    expect($executed)->toBe(0);
    Queue::assertNothingPushed();
});

it('is idempotent: running twice on the same day runs once', function () {
    Queue::fake();
    $date = CarbonImmutable::parse('2026-03-15');
    $p = Portfolio::factory()->create(['schedule_days' => [15]]);
    $p->monitoredCompanies()->create(['cnpj' => '11222333000181']);

    app(RunDuePortfolioSchedules::class)->handle($date);
    $executed = app(RunDuePortfolioSchedules::class)->handle($date);

    expect($executed)->toBe(0)
        ->and(PortfolioScheduledRun::where('portfolio_id', $p->id)->count())->toBe(1);
});

it('skips portfolios without a schedule', function () {
    Queue::fake();
    Portfolio::factory()->create(['schedule_days' => null]);

    $executed = app(RunDuePortfolioSchedules::class)->handle(CarbonImmutable::parse('2026-03-15'));

    expect($executed)->toBe(0);
    Queue::assertNothingPushed();
});

it('runs the same portfolio on each of its scheduled days across the month', function () {
    Queue::fake();
    $p = Portfolio::factory()->create(['schedule_days' => [5, 20]]);
    $p->monitoredCompanies()->create(['cnpj' => '11222333000181']);

    app(RunDuePortfolioSchedules::class)->handle(CarbonImmutable::parse('2026-03-05'));
    app(RunDuePortfolioSchedules::class)->handle(CarbonImmutable::parse('2026-03-12')); // não agendado
    app(RunDuePortfolioSchedules::class)->handle(CarbonImmutable::parse('2026-03-20'));

    $runs = PortfolioScheduledRun::where('portfolio_id', $p->id)->orderBy('ran_on')->get();

    expect($runs)->toHaveCount(2)
        ->and($runs[0]->scheduled_day)->toBe(5)
        ->and($runs[0]->ran_on->toDateString())->toBe('2026-03-05')
        ->and($runs[1]->scheduled_day)->toBe(20)
        ->and($runs[1]->ran_on->toDateString())->toBe('2026-03-20');
});

it('does not double-enqueue a company that already has a pending refresh (dedup, DT-1)', function () {
    Queue::fake();
    $date = CarbonImmutable::parse('2026-03-15');
    $p = Portfolio::factory()->create(['schedule_days' => [15]]);
    $company = $p->monitoredCompanies()->create(['cnpj' => '11222333000181']);

    RefreshMonitoredCompanyJob::dispatch($company); // já pendente

    app(RunDuePortfolioSchedules::class)->handle($date);

    // ShouldBeUnique impede o segundo enfileiramento do lote agendado.
    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 1);
});
