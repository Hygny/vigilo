<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Portfolio;
use App\Support\ScheduleCalendar;
use Carbon\CarbonImmutable;

/**
 * Dispara os refreshes dos portfólios agendados para uma data e registra cada
 * execução. Chamado 1×/dia pelo scheduler.
 *
 * O dispatch usa QueuePortfolioRefresh (jobs ShouldBeUnique), então o lote é
 * naturalmente deduplicado — sem o problema do antigo Bus::batch (DT-1).
 */
final class RunDuePortfolioSchedules
{
    public function __construct(private readonly QueuePortfolioRefresh $refresher) {}

    /**
     * @return int número de portfólios executados nesta data
     */
    public function handle(?CarbonImmutable $date = null): int
    {
        $date = $date ?? CarbonImmutable::now();
        $day = $date->day;
        $daysInMonth = $date->daysInMonth;
        $ranOn = $date->toDateString();

        $executed = 0;

        Portfolio::query()
            ->whereNotNull('schedule_days')
            ->chunkById(200, function ($portfolios) use ($day, $daysInMonth, $ranOn, &$executed): void {
                foreach ($portfolios as $portfolio) {
                    $matchedDay = ScheduleCalendar::matchedDay($portfolio->schedule_days ?? [], $day, $daysInMonth);

                    if ($matchedDay === null) {
                        continue;
                    }

                    // Idempotência: no máximo uma execução por dia (a unique key
                    // (portfolio_id, ran_on) é a garantia final).
                    if ($portfolio->scheduledRuns()->whereDate('ran_on', $ranOn)->exists()) {
                        continue;
                    }

                    $companiesCount = $portfolio->monitoredCompanies()->count();
                    $dispatched = $this->refresher->handle($portfolio);

                    $portfolio->scheduledRuns()->create([
                        'scheduled_day' => $matchedDay,
                        'ran_on' => $ranOn,
                        'companies_count' => $companiesCount,
                        'dispatched_count' => $dispatched,
                    ]);

                    $executed++;
                }
            });

        return $executed;
    }
}
