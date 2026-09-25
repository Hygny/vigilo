<?php

declare(strict_types=1);

// Smoke test dos caminhos de asset da marca: pega um asset()/caminho quebrado
// sem depender de inspeção visual (a renderização em si é o débito DT-9).

it('renders the login page with the Vigilo brand logo for both themes', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('brand/vigilo-logo-light.png')
        ->assertSee('brand/vigilo-logo-dark.png');
});

it('links the favicon, apple-touch icon and web manifest on the login page', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('favicon.ico')
        ->assertSee('brand/apple-touch-icon.png')
        ->assertSee('site.webmanifest');
});
