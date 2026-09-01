<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Schedule;

it('schedules the per-portfolio refresh daily at 03:00', function () {
    // Executing the command forces routes/console.php (and its schedule) to load.
    $this->artisan('schedule:list')->assertExitCode(0);

    $event = collect(app(Schedule::class)->events())
        ->first(fn ($e): bool => $e->description === 'vigilo-portfolio-schedules');

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('0 3 * * *');
});
