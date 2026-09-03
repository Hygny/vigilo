<?php

declare(strict_types=1);

it('redirects the root to the login screen', function () {
    $this->get('/')->assertRedirect('/login');
});
