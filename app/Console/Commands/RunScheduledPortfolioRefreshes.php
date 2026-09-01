<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\RunDuePortfolioSchedules;
use Illuminate\Console\Command;

class RunScheduledPortfolioRefreshes extends Command
{
    protected $signature = 'portfolios:run-scheduled';

    protected $description = 'Dispara os refreshes dos portfólios agendados para hoje.';

    public function handle(RunDuePortfolioSchedules $action): int
    {
        $executed = $action->handle();

        $this->info("Portfólios agendados executados hoje: {$executed}.");

        return self::SUCCESS;
    }
}
