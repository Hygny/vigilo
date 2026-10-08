<?php

declare(strict_types=1);

use App\Livewire\Admin\Organizations\Index;
use App\Models\MaintenanceRun;
use App\Models\User;
use Livewire\Livewire;

function recordMaintenance(array $attrs = []): MaintenanceRun
{
    return MaintenanceRun::create(array_merge([
        'kind' => 'cnpj_mensal',
        'status' => 'success',
        'reimport_status' => 'ok',
        'recoleta_status' => 'ok',
        'normalizar_status' => 'ok',
        'started_at' => now()->subHour(),
        'finished_at' => now(),
    ], $attrs));
}

it('shows "no runs yet" when there is no maintenance run', function () {
    $super = User::factory()->superAdmin()->create();

    Livewire::actingAs($super)->test(Index::class)
        ->assertSee('Base CNPJ — manutenção mensal')
        ->assertSee('Nenhuma execução registrada');
});

it('shows the last successful run with its steps', function () {
    $super = User::factory()->superAdmin()->create();
    recordMaintenance();

    Livewire::actingAs($super)->test(Index::class)
        ->assertSee('Sucesso')
        ->assertSee('Reimport')
        ->assertSee('Re-coleta')
        ->assertDontSee('Atrasado');
});

it('shows a failed run with its message', function () {
    $super = User::factory()->superAdmin()->create();
    recordMaintenance([
        'status' => 'failed',
        'reimport_status' => 'fail',
        'recoleta_status' => 'skip',
        'normalizar_status' => 'skip',
        'message' => 'reimport falhou',
    ]);

    Livewire::actingAs($super)->test(Index::class)
        ->assertSee('Falhou')
        ->assertSee('reimport falhou');
});

it('flags a stale run when the last run is older than the window', function () {
    $super = User::factory()->superAdmin()->create();
    recordMaintenance(['finished_at' => now()->subDays(14)]);

    Livewire::actingAs($super)->test(Index::class)
        ->assertSee('Atrasado')
        ->assertSee('agendamento pode ter parado');
});
