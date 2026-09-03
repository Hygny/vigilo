<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Livewire\Admin\Users;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

/**
 * @return array{0: Organization, 1: User}
 */
function orgWithAdmin(): array
{
    $org = Organization::factory()->create();
    $admin = User::factory()->for($org)->admin()->create();

    return [$org, $admin];
}

it('lets an admin create a user in their organization', function () {
    [$org, $admin] = orgWithAdmin();

    Livewire::actingAs($admin)->test(Users::class)
        ->set('name', 'Maria Silva')
        ->set('email', 'maria@empresa.com')
        ->set('password', 'senha-forte-123')
        ->set('role', 'user')
        ->call('createUser')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'email' => 'maria@empresa.com',
        'organization_id' => $org->id,
        'role' => 'user',
    ]);
});

it('creates an admin when the admin role is chosen', function () {
    [, $admin] = orgWithAdmin();

    Livewire::actingAs($admin)->test(Users::class)
        ->set('name', 'Chefe')
        ->set('email', 'chefe@empresa.com')
        ->set('password', 'senha-forte-123')
        ->set('role', 'admin')
        ->call('createUser')
        ->assertHasNoErrors();

    expect(User::where('email', 'chefe@empresa.com')->first()->role)->toBe(Role::Admin);
});

it('validates name, email and password of the new user', function () {
    [, $admin] = orgWithAdmin();

    Livewire::actingAs($admin)->test(Users::class)
        ->set('name', 'X')->set('email', '')->set('password', '')
        ->call('createUser')
        ->assertHasErrors(['name', 'email', 'password']);
});

it('lets an admin change a user role', function () {
    [$org, $admin] = orgWithAdmin();
    $user = User::factory()->for($org)->create();

    Livewire::actingAs($admin)->test(Users::class)->call('setRole', $user->id, 'admin');

    expect($user->fresh()->role)->toBe(Role::Admin);
});

it('lets an admin delete another user', function () {
    [$org, $admin] = orgWithAdmin();
    $user = User::factory()->for($org)->create();

    Livewire::actingAs($admin)->test(Users::class)->call('deleteUser', $user->id);

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('does not let an admin delete themselves', function () {
    [$org, $admin] = orgWithAdmin();
    User::factory()->for($org)->admin()->create(); // outro admin, p/ não ser o último

    Livewire::actingAs($admin)->test(Users::class)
        ->call('deleteUser', $admin->id)
        ->assertHasErrors('delete');

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

it('never lets the organization run out of admins', function () {
    [$org, $admin] = orgWithAdmin(); // único admin da org
    User::factory()->for($org)->create();

    Livewire::actingAs($admin)->test(Users::class)
        ->call('setRole', $admin->id, 'user')
        ->assertHasErrors('role');

    expect($admin->fresh()->role)->toBe(Role::Admin);
});

it('scopes management to the admin own organization', function () {
    [, $adminA] = orgWithAdmin();
    $foreign = User::factory()->create(); // outra organização

    Livewire::actingAs($adminA)->test(Users::class)->assertDontSee($foreign->email);

    expect(fn () => Livewire::actingAs($adminA)->test(Users::class)->call('deleteUser', $foreign->id))
        ->toThrow(ModelNotFoundException::class);
});

it('forbids non-admins from the users screen', function () {
    $user = User::factory()->create(); // papel usuário

    $this->actingAs($user)->get(route('users.index'))->assertForbidden();
});

it('redirects guests from the users screen to login', function () {
    $this->get(route('users.index'))->assertRedirect(route('login'));
});
