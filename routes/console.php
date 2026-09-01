<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// KYB contínuo: todo dia às 03:00 dispara os refreshes dos portfólios agendados
// para aquele dia (dia configurável, e vários dias, por portfólio).
Schedule::command('portfolios:run-scheduled')
    ->dailyAt('03:00')
    ->name('vigilo-portfolio-schedules')
    ->withoutOverlapping();
