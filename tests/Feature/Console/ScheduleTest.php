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

it('schedules the sanctions sweep twice a night in Brasília time, with a bounded mutex', function () {
    $this->artisan('schedule:list')->assertExitCode(0);

    $event = collect(app(Schedule::class)->events())
        ->first(fn ($e): bool => $e->description === 'vigilo-sweep-sanctions');

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('0 1,4 * * *')     // 2ª passada retoma a 1ª
        ->and($event->timezone)->toBe('America/Sao_Paulo') // janela folgada da CGU (app em UTC)
        ->and($event->withoutOverlapping)->toBeTrue()
        ->and($event->expiresAt)->toBe(170)                // < 3h: libera antes da passada das 04:00
        ->and($event->runInBackground)->toBeTrue();
});
