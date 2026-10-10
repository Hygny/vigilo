<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

it('keeps role and organization_id out of the fillable (set server-side only)', function () {
    $fillable = (new User)->getFillable();

    expect($fillable)->not->toContain('role')
        ->and($fillable)->not->toContain('organization_id')
        ->and($fillable)->toContain('name')
        ->and($fillable)->toContain('email');
});

it('drops role and organization_id on mass assignment (no privilege escalation)', function () {
    $user = (new User)->fill([
        'name' => 'X',
        'email' => 'x@example.com',
        'password' => 'secret-123',
        'role' => 'admin',          // tentativa de escalonar
        'organization_id' => 999,   // tentativa de auto-atribuir tenant
    ]);

    expect($user->getAttribute('role'))->toBeNull()
        ->and($user->getAttribute('organization_id'))->toBeNull()
        ->and($user->name)->toBe('X'); // campos legítimos passam
});

it('seeds the demo admin with an organization and the admin role (forceFill)', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::query()->where('email', 'demo@vigilo.test')->sole();

    expect($admin->role)->toBe(Role::Admin)
        ->and($admin->organization_id)->not->toBeNull()
        ->and($admin->email_verified_at)->not->toBeNull();
});
