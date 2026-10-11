<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('prints the sanctions tied to the CNPJ', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(function (Request $request) {
        if (str_contains($request->url(), '/ceis')) {
            return Http::response([[
                'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
                'tipoSancao' => ['descricaoResumida' => 'Empresa inidônea'],
                'orgaoSancionador' => ['nome' => 'TCU'],
                'dataInicioSancao' => '01/03/2023',
            ]]);
        }

        return Http::response([]);
    });

    $this->artisan('vigilo:risco', ['cnpj' => '11.222.333/0001-81'])
        ->expectsOutputToContain('11222333000181') // linha de cabeçalho: "N sanção(ões) para <cnpj>:"
        ->expectsOutputToContain('CEIS')            // a sanção foi listada na tabela
        ->assertSuccessful();
});

it('reports "nothing on record" for a clean CNPJ', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(fn () => Http::response([]));

    $this->artisan('vigilo:risco', ['cnpj' => '11222333000181'])
        ->expectsOutputToContain('Nada consta')
        ->assertSuccessful();
});

it('fails when the source is unavailable', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(fn () => Http::response('erro', 500));

    $this->artisan('vigilo:risco', ['cnpj' => '11222333000181'])
        ->expectsOutputToContain('indisponível')
        ->assertFailed();
});

it('fails when the integration is not configured', function () {
    config()->set('risco.portal.api_key', null);
    Http::fake();

    $this->artisan('vigilo:risco', ['cnpj' => '11222333000181'])
        ->assertFailed();

    Http::assertNothingSent();
});
