<?php

declare(strict_types=1);

use App\Rules\PublicHttpsUrl;

function urlPasses(string $url): bool
{
    $failed = false;
    (new PublicHttpsUrl)->validate('u', $url, function () use (&$failed): void {
        $failed = true;
    });

    return ! $failed;
}

it('blocks IPv4 disguised as a hostname (decimal/octal/hex/short forms)', function (string $url) {
    expect(urlPasses($url))->toBeFalse();
})->with([
    'https://2130706433/hook',    // 127.0.0.1 em decimal
    'https://127.1/hook',         // curto
    'https://0177.0.0.1/hook',    // octal
    'https://0x7f.0.0.1/hook',    // hex por rótulo
    'https://0x7f000001/hook',    // hex inteiro
]);

it('blocks literal loopback/private IPs and localhost', function (string $url) {
    expect(urlPasses($url))->toBeFalse();
})->with([
    'https://127.0.0.1/hook',
    'https://10.0.0.5/hook',
    'https://192.168.1.1/hook',
    'https://169.254.169.254/hook', // metadata link-local
    'https://localhost/hook',
    'http://consumidor.test/hook',  // não-https
    'https://./hook',               // host degenerado → vazio após normalizar
]);

it('allows a genuine public https host', function (string $url) {
    expect(urlPasses($url))->toBeTrue();
})->with([
    'https://consumidor.test/hook',
    'https://hooks.example.com/webhooks/vigilo',
    'https://123cliente.com.br/hook', // começa com dígitos, mas TLD alfabético
    'https://consumidor.test./hook',  // FQDN com ponto final → normalizado
]);
