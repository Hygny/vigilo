<?php

declare(strict_types=1);

use App\Livewire\Admin\Organizations\Show;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

it('lets the super-admin impersonate a user and audits it', function () {
    $super = User::factory()->superAdmin()->create();
    $org = Organization::factory()->create();
    $target = User::factory()->for($org)->create(['email' => 'alvo@org.com']);

    Livewire::actingAs($super)->test(Show::class, ['organization' => $org])
        ->call('impersonate', $target->id)
        ->assertRedirect(route('dashboard'));

    expect(auth()->id())->toBe($target->id)
        ->and(session('impersonator_id'))->toBe($super->id);

    $this->assertDatabaseHas('impersonation_logs', [
        'impersonator_id' => $super->id,
        'impersonator_email' => $super->email,
        'impersonated_user_id' => $target->id,
        'impersonated_email' => 'alvo@org.com',
        'organization_id' => $org->id,
    ]);
});

it('never impersonates another super-admin', function () {
    $super = User::factory()->superAdmin()->create();
    $org = Organization::factory()->create();
    $otherSuper = User::factory()->for($org)->create();
    $otherSuper->forceFill(['is_super_admin' => true])->save();

    Livewire::actingAs($super)->test(Show::class, ['organization' => $org])
        ->call('impersonate', $otherSuper->id);

    expect(auth()->id())->toBe($super->id); // não trocou de identidade
    $this->assertDatabaseCount('impersonation_logs', 0);
});

it('forbids a non super-admin from the organization detail (and its actions)', function () {
    $org = Organization::factory()->create();
    $admin = User::factory()->for($org)->admin()->create();

    $this->actingAs($admin)->get(route('admin.organizations.show', $org))->assertForbidden();
});

it('stops impersonating and restores the super-admin', function () {
    $super = User::factory()->superAdmin()->create();
    $org = Organization::factory()->create();
    $target = User::factory()->for($org)->create();

    $this->actingAs($target);
    session(['impersonator_id' => $super->id]);

    $this->post(route('impersonate.stop'))->assertRedirect(route('admin.organizations.index'));

    expect(auth()->id())->toBe($super->id)
        ->and(session('impersonator_id'))->toBeNull();
});

it('does not suspend-kick a session that is impersonating', function () {
    $super = User::factory()->superAdmin()->create();
    $org = Organization::factory()->create(['suspended_at' => now()]);
    $target = User::factory()->for($org)->create();

    $this->actingAs($target);
    session(['impersonator_id' => $super->id]);

    // Sem personificação seria deslogado; personificando, o operador continua.
    $this->get(route('dashboard'))->assertOk();
});

it('forbids a non super-admin from driving the organization detail component', function () {
    $org = Organization::factory()->create();
    $admin = User::factory()->for($org)->admin()->create();

    Livewire::actingAs($admin)->test(Show::class, ['organization' => $org])
        ->assertForbidden(); // mount() faz abort_unless(..., 403)
});

it('re-checks super-admin inside the impersonate action, not only on mount', function () {
    // O POST /livewire/update não re-roda mount; o abort_unless no corpo é o gate real.
    $super = User::factory()->superAdmin()->create();
    $org = Organization::factory()->create();
    $target = User::factory()->for($org)->create();

    $component = Livewire::actingAs($super)->test(Show::class, ['organization' => $org]);

    // Privilégio perdido depois do mount (a instância autenticada é a mesma $super).
    $super->forceFill(['is_super_admin' => false])->save();

    $component->call('impersonate', $target->id)->assertForbidden();

    expect(auth()->id())->toBe($super->id); // não trocou de identidade
    $this->assertDatabaseCount('impersonation_logs', 0);
});

it('cannot impersonate a user from another organization', function () {
    $super = User::factory()->superAdmin()->create();
    $orgA = Organization::factory()->create();
    $orgB = Organization::factory()->create();
    $foreign = User::factory()->for($orgB)->create();

    expect(fn () => Livewire::actingAs($super)->test(Show::class, ['organization' => $orgA])
        ->call('impersonate', $foreign->id))
        ->toThrow(ModelNotFoundException::class);

    $this->assertDatabaseCount('impersonation_logs', 0);
});
