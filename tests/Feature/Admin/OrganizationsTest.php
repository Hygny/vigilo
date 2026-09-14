<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Livewire\Admin\Organizations\Index;
use App\Livewire\Admin\Organizations\Show;
use App\Models\Organization;
use App\Models\User;
use Livewire\Livewire;
use Livewire\Volt\Volt;

// ---------------------------------------------------------------------------
// Acesso ao painel /admin
// ---------------------------------------------------------------------------

it('redirects guests from the admin panel to login', function () {
    $this->get(route('admin.organizations.index'))->assertRedirect(route('login'));
});

it('forbids a regular user from the admin panel', function () {
    $user = User::factory()->create(); // usuário comum, com organização

    $this->actingAs($user)->get(route('admin.organizations.index'))->assertForbidden();
});

it('forbids an organization admin from the admin panel', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.organizations.index'))->assertForbidden();
});

it('lets the super-admin open the admin panel', function () {
    $super = User::factory()->superAdmin()->create();

    $this->actingAs($super)->get(route('admin.organizations.index'))->assertOk();
});

// ---------------------------------------------------------------------------
// Listagem cross-tenant
// ---------------------------------------------------------------------------

it('lists organizations from every tenant to the super-admin', function () {
    $super = User::factory()->superAdmin()->create();
    Organization::factory()->create(['name' => 'Alpha Org']);
    Organization::factory()->create(['name' => 'Beta Org']);

    Livewire::actingAs($super)->test(Index::class)
        ->assertSee('Alpha Org')
        ->assertSee('Beta Org');
});

// ---------------------------------------------------------------------------
// Onboardar organização + primeiro admin
// ---------------------------------------------------------------------------

it('lets the super-admin onboard an organization with a verified admin', function () {
    $super = User::factory()->superAdmin()->create();

    Livewire::actingAs($super)->test(Index::class)
        ->set('orgName', 'Acme Ltda')
        ->set('adminName', 'Maria Silva')
        ->set('adminEmail', 'maria@acme.com')
        ->set('adminPassword', 'senha-forte-123')
        ->call('createOrganization')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('organizations', ['name' => 'Acme Ltda']);

    $admin = User::whereEmail('maria@acme.com')->firstOrFail();
    $org = Organization::whereName('Acme Ltda')->firstOrFail();

    expect($admin->role)->toBe(Role::Admin)
        ->and($admin->organization_id)->toBe($org->id)
        ->and($admin->is_super_admin)->toBeFalse()
        ->and($admin->hasVerifiedEmail())->toBeTrue(); // não cai no middleware 'verified'
});

it('validates the onboarding form', function () {
    $super = User::factory()->superAdmin()->create();

    Livewire::actingAs($super)->test(Index::class)
        ->set('orgName', '')
        ->set('adminName', '')
        ->set('adminEmail', 'nao-email')
        ->set('adminPassword', '123')
        ->call('createOrganization')
        ->assertHasErrors(['orgName', 'adminName', 'adminEmail', 'adminPassword']);
});

it('rejects onboarding with a duplicate admin email', function () {
    $super = User::factory()->superAdmin()->create();
    User::factory()->create(['email' => 'ocupado@acme.com']);

    Livewire::actingAs($super)->test(Index::class)
        ->set('orgName', 'Acme Ltda')
        ->set('adminName', 'Maria')
        ->set('adminEmail', 'ocupado@acme.com')
        ->set('adminPassword', 'senha-forte-123')
        ->call('createOrganization')
        ->assertHasErrors('adminEmail');
});

// ---------------------------------------------------------------------------
// Suspender / reativar
// ---------------------------------------------------------------------------

it('lets the super-admin suspend and reactivate an organization', function () {
    $super = User::factory()->superAdmin()->create();
    $org = Organization::factory()->create();

    Livewire::actingAs($super)->test(Show::class, ['organization' => $org])->call('suspend');
    expect($org->fresh()->isSuspended())->toBeTrue();

    Livewire::actingAs($super)->test(Show::class, ['organization' => $org])->call('reactivate');
    expect($org->fresh()->isSuspended())->toBeFalse();
});

it('blocks login for a user of a suspended organization', function () {
    $org = Organization::factory()->create(['suspended_at' => now()]);
    User::factory()->for($org)->create(['email' => 'preso@org.com']);

    Volt::test('pages.auth.login')
        ->set('form.email', 'preso@org.com')
        ->set('form.password', 'password')
        ->call('login')
        ->assertHasErrors('form.email');

    $this->assertGuest();
});

it('logs out mid-session a user whose organization gets suspended', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();

    $org->forceFill(['suspended_at' => now()])->save();
    // Em produção cada request recarrega o usuário do banco; o actingAs reusa a
    // mesma instância, então recarregamos para refletir a org suspensa.
    $user->refresh();

    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->assertGuest();
});

// ---------------------------------------------------------------------------
// Promover / rebaixar papel em qualquer org
// ---------------------------------------------------------------------------

it('lets the super-admin change a user role in any organization', function () {
    $super = User::factory()->superAdmin()->create();
    $org = Organization::factory()->create();
    User::factory()->for($org)->admin()->create(); // garante outro admin
    $user = User::factory()->for($org)->create();

    Livewire::actingAs($super)->test(Show::class, ['organization' => $org])
        ->call('setRole', $user->id, 'admin');

    expect($user->fresh()->role)->toBe(Role::Admin);
});

it('never lets an organization run out of admins via the panel', function () {
    $super = User::factory()->superAdmin()->create();
    $org = Organization::factory()->create();
    $onlyAdmin = User::factory()->for($org)->admin()->create();

    Livewire::actingAs($super)->test(Show::class, ['organization' => $org])
        ->call('setRole', $onlyAdmin->id, 'user')
        ->assertHasErrors('role');

    expect($onlyAdmin->fresh()->role)->toBe(Role::Admin);
});

// ---------------------------------------------------------------------------
// Login do super-admin vai para o /admin
// ---------------------------------------------------------------------------

it('sends the super-admin to the admin panel on login', function () {
    User::factory()->superAdmin()->create(['email' => 'dono@vigilo.test']);

    Volt::test('pages.auth.login')
        ->set('form.email', 'dono@vigilo.test')
        ->set('form.password', 'password')
        ->call('login')
        ->assertRedirect(route('admin.organizations.index', absolute: false));
});
