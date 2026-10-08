<?php

declare(strict_types=1);

use App\Models\MaintenanceRun;

it('records a successful run with the parsed start time', function () {
    $this->artisan('vigilo:maintenance-record', [
        '--reimport' => 'ok',
        '--recoleta' => 'ok',
        '--normalizar' => 'ok',
        '--started' => '2026-11-05T03:00:00+00:00',
    ])->assertSuccessful();

    $run = MaintenanceRun::sole();

    expect($run->status)->toBe('success')
        ->and($run->reimport_status)->toBe('ok')
        ->and($run->recoleta_status)->toBe('ok')
        ->and($run->normalizar_status)->toBe('ok')
        ->and($run->started_at?->format('Y-m-d'))->toBe('2026-11-05')
        ->and($run->finished_at)->not->toBeNull()
        ->and($run->message)->toBeNull();
});

it('records a failed run with a message (any step = fail → failed)', function () {
    $this->artisan('vigilo:maintenance-record', [
        '--reimport' => 'fail',
        '--message' => 'reimport falhou',
    ])->assertSuccessful();

    $run = MaintenanceRun::sole();

    expect($run->status)->toBe('failed')
        ->and($run->reimport_status)->toBe('fail')
        ->and($run->recoleta_status)->toBe('skip') // default quando não informado
        ->and($run->normalizar_status)->toBe('skip')
        ->and($run->message)->toBe('reimport falhou');
});
