<?php

declare(strict_types=1);

use App\Livewire\Admin\Organizations\Show;
use App\Models\Organization;
use App\Models\User;
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
