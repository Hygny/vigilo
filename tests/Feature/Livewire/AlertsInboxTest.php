<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Livewire\Alerts\Inbox;
use App\Models\ChangeEvent;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

function ownedCompany(User $user): MonitoredCompany
{
    return MonitoredCompany::factory()
        ->for(Portfolio::factory()->for($user->organization))
        ->create();
}

it('lists open alerts and acknowledges one', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $company = ownedCompany($user);
    $event = ChangeEvent::factory()->for($company, 'monitoredCompany')->create();

    Livewire::actingAs($user)->test(Inbox::class)
        ->assertOk()
        ->call('acknowledge', $event->id)
        ->assertHasNoErrors();

    expect($event->fresh()->acknowledged_at)->not->toBeNull();
});

it('hides alerts from other organizations', function () {
    $user = User::factory()->for(Organization::factory())->create();

    $foreignCompany = MonitoredCompany::factory()->create();
    ChangeEvent::factory()->for($foreignCompany, 'monitoredCompany')->create();

    Livewire::actingAs($user)->test(Inbox::class)
        ->assertViewHas('events', fn ($events): bool => $events->isEmpty());
});

it('filters alerts by severity', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $company = ownedCompany($user);

    $critical = ChangeEvent::factory()->for($company, 'monitoredCompany')->create([
        'severity' => Severity::Critical,
    ]);
    ChangeEvent::factory()->for($company, 'monitoredCompany')->create([
        'severity' => Severity::Low,
        'type' => ChangeType::NameChanged,
        'field' => 'razao_social',
    ]);

    Livewire::actingAs($user)->test(Inbox::class)
        ->call('setSeverity', 'critical')
        ->assertViewHas('events', fn ($events): bool => $events->count() === 1 && $events->first()->is($critical));
});

it('cannot acknowledge an alert from another organization', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $foreignCompany = MonitoredCompany::factory()->create();
    $foreignEvent = ChangeEvent::factory()->for($foreignCompany, 'monitoredCompany')->create();

    $component = Livewire::actingAs($user)->test(Inbox::class);

    // The event is outside the org scope, so it is simply not found.
    expect(fn () => $component->call('acknowledge', $foreignEvent->id))
        ->toThrow(ModelNotFoundException::class);

    expect($foreignEvent->fresh()->acknowledged_at)->toBeNull();
});

it('acknowledges only the alerts of the active severity filter', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $company = ownedCompany($user);

    $critical = ChangeEvent::factory()->for($company, 'monitoredCompany')->create([
        'severity' => Severity::Critical,
    ]);
    $low = ChangeEvent::factory()->for($company, 'monitoredCompany')->create([
        'severity' => Severity::Low,
        'type' => ChangeType::NameChanged,
        'field' => 'razao_social',
    ]);

    Livewire::actingAs($user)->test(Inbox::class)
        ->call('setSeverity', 'low')
        ->call('acknowledgeAll');

    // Só os "baixos" (filtro ativo) foram reconhecidos; o crítico segue em aberto.
    expect($low->fresh()->acknowledged_at)->not->toBeNull()
        ->and($critical->fresh()->acknowledged_at)->toBeNull();
});
