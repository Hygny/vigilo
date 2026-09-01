<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response
        ->assertOk()
        ->assertSeeVolt('pages.auth.register');
});

test('new users can register and get an organization', function () {
    $component = Volt::test('pages.auth.register')
        ->set('organization', 'Acme Ltda')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register');

    $component->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $user = User::whereEmail('test@example.com')->firstOrFail();

    expect($user->organization_id)->not->toBeNull()
        ->and($user->organization->name)->toBe('Acme Ltda');

    $this->assertDatabaseHas('organizations', ['name' => 'Acme Ltda']);
});

test('registration requires an organization name', function () {
    Volt::test('pages.auth.register')
        ->set('organization', '')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertHasErrors('organization');

    $this->assertGuest();
});
