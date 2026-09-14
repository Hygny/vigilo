<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Route;

test('public self-registration is disabled', function () {
    // Segurança multi-tenant: não há autocadastro. A rota /register foi removida
    // (organizações são onboardadas manualmente; usuários via tela admin).
    expect(Route::has('register'))->toBeFalse();

    $this->get('/register')->assertNotFound();
});
