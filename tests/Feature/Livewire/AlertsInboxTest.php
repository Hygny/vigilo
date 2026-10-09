<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Enums\TriageStatus;
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

it('starts analysis on an alert', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $company = ownedCompany($user);
    $event = ChangeEvent::factory()->for($company, 'monitoredCompany')->create();

    Livewire::actingAs($user)->test(Inbox::class)
        ->assertOk()
        ->call('startAnalysis', $event->id)
        ->assertHasNoErrors();

    $event->refresh();
    expect($event->triage_status)->toBe(TriageStatus::EmAnalise)
        ->and($event->triaged_at)->not->toBeNull()
        ->and($event->triaged_by_id)->toBe($user->id);
});

it('promotes an alert to a case', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $event = ChangeEvent::factory()->for(ownedCompany($user), 'monitoredCompany')->create();

    Livewire::actingAs($user)->test(Inbox::class)->call('promoteToCase', $event->id);

    expect($event->fresh()->triage_status)->toBe(TriageStatus::Caso);
});

it('requires a reason to dismiss an alert', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $event = ChangeEvent::factory()->for(ownedCompany($user), 'monitoredCompany')->create();

    Livewire::actingAs($user)->test(Inbox::class)
        ->call('beginDismiss', $event->id)
        ->set('dismissReason', '')
        ->call('confirmDismiss')
        ->assertHasErrors('dismissReason');

    expect($event->fresh()->triage_status)->toBe(TriageStatus::Novo);
});

it('dismisses an alert with a reason', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $event = ChangeEvent::factory()->for(ownedCompany($user), 'monitoredCompany')->create();

    Livewire::actingAs($user)->test(Inbox::class)
        ->call('beginDismiss', $event->id)
        ->set('dismissReason', 'filial encerrada, matriz ativa')
        ->call('confirmDismiss')
        ->assertHasNoErrors();

    $event->refresh();
    expect($event->triage_status)->toBe(TriageStatus::Descartado)
        ->and($event->triage_reason)->toBe('filial encerrada, matriz ativa')
        ->and($event->triaged_by_id)->toBe($user->id);
});

it('reopens a resolved alert and clears the reason', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $event = ChangeEvent::factory()->for(ownedCompany($user), 'monitoredCompany')->create([
        'triage_status' => TriageStatus::Descartado,
        'triage_reason' => 'descartei antes',
    ]);

    Livewire::actingAs($user)->test(Inbox::class)->call('reopen', $event->id);

    $event->refresh();
    expect($event->triage_status)->toBe(TriageStatus::Novo)
        ->and($event->triage_reason)->toBeNull();
});

it('hides alerts from other organizations', function () {
    $user = User::factory()->for(Organization::factory())->create();

    $foreignCompany = MonitoredCompany::factory()->create();
    ChangeEvent::factory()->for($foreignCompany, 'monitoredCompany')->create();

    Livewire::actingAs($user)->test(Inbox::class)
        ->assertViewHas('events', fn ($events): bool => $events->isEmpty());
});

it('cannot triage an alert from another organization', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $foreignCompany = MonitoredCompany::factory()->create();
    $foreignEvent = ChangeEvent::factory()->for($foreignCompany, 'monitoredCompany')->create();

    $component = Livewire::actingAs($user)->test(Inbox::class);

    expect(fn () => $component->call('startAnalysis', $foreignEvent->id))
        ->toThrow(ModelNotFoundException::class);

    expect($foreignEvent->fresh()->triage_status)->toBe(TriageStatus::Novo);
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

it('filters alerts by triage status', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $company = ownedCompany($user);

    $novo = ChangeEvent::factory()->for($company, 'monitoredCompany')->create();
    $descartado = ChangeEvent::factory()->for($company, 'monitoredCompany')->create([
        'type' => ChangeType::NameChanged, 'field' => 'razao_social',
        'triage_status' => TriageStatus::Descartado,
    ]);
    $caso = ChangeEvent::factory()->for($company, 'monitoredCompany')->create([
        'type' => ChangeType::AddressChanged, 'field' => 'endereco',
        'triage_status' => TriageStatus::Caso,
    ]);

    $ids = fn ($events): array => $events->pluck('id')->all();

    $component = Livewire::actingAs($user)->test(Inbox::class);

    // Padrão "abertos": só o novo.
    $component->assertViewHas('events', fn ($e): bool => $ids($e) === [$novo->id]);

    $component->call('setTriage', 'descartados')
        ->assertViewHas('events', fn ($e): bool => $ids($e) === [$descartado->id]);

    $component->call('setTriage', 'casos')
        ->assertViewHas('events', fn ($e): bool => $ids($e) === [$caso->id]);

    $component->call('setTriage', 'todos')
        ->assertViewHas('events', fn ($e): bool => count($ids($e)) === 3);
});
