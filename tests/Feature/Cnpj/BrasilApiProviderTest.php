<?php

declare(strict_types=1);

use App\DTO\CompanyData;
use App\Providers\Cnpj\BrasilApiProvider;
use App\Providers\Cnpj\Exceptions\CnpjProviderException;
use App\Support\Exceptions\InvalidCnpjException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

const BASE_URL = 'https://brasilapi.com.br/api/cnpj/v1';

/**
 * @return array<string, mixed>
 */
function brasilApiPayload(): array
{
    return [
        'cnpj' => '11222333000181',
        'razao_social' => 'EMPRESA EXEMPLO LTDA',
        'nome_fantasia' => 'EXEMPLO',
        'situacao_cadastral' => 2,
        'descricao_situacao_cadastral' => 'ATIVA',
        'data_situacao_cadastral' => '2005-11-03',
        'cnae_fiscal' => 6201501,
        'cnae_fiscal_descricao' => 'Desenvolvimento de programas de computador sob encomenda',
        'porte' => 'DEMAIS',
        'natureza_juridica' => '206-2 - Sociedade Empresária Limitada',
        'logradouro' => 'RUA DAS FLORES',
        'numero' => '100',
        'municipio' => 'SAO PAULO',
        'uf' => 'SP',
        'qsa' => [
            ['nome_socio' => 'FULANO DE TAL', 'cnpj_cpf_do_socio' => '***123456**', 'qualificacao_socio' => 'Sócio-Administrador'],
            ['nome_socio' => 'BELTRANO DA SILVA', 'cnpj_cpf_do_socio' => '***654321**', 'qualificacao_socio' => 'Sócio'],
        ],
    ];
}

function makeProvider(int $tries = 3, int $throttlePerMinute = 0): BrasilApiProvider
{
    return new BrasilApiProvider(BASE_URL, 10, $tries, 0, $throttlePerMinute);
}

it('maps a BrasilAPI payload into a CompanyData DTO', function () {
    Http::fake([BASE_URL.'/*' => Http::response(brasilApiPayload(), 200)]);

    $data = makeProvider()->fetch('11.222.333/0001-81');

    expect($data)->toBeInstanceOf(CompanyData::class)
        ->and($data->cnpj)->toBe('11222333000181')
        ->and($data->razaoSocial)->toBe('EMPRESA EXEMPLO LTDA')
        ->and($data->nomeFantasia)->toBe('EXEMPLO')
        ->and($data->situacaoCadastral)->toBe('ATIVA')
        ->and($data->situacaoData)->toBe('2005-11-03')
        ->and($data->cnaePrincipal)->toBe('6201501')
        ->and($data->porte)->toBe('DEMAIS')
        ->and($data->naturezaJuridica)->toBe('206-2 - Sociedade Empresária Limitada')
        ->and($data->logradouro)->toBe('RUA DAS FLORES, 100')
        ->and($data->municipio)->toBe('SAO PAULO')
        ->and($data->uf)->toBe('SP')
        ->and($data->partners)->toHaveCount(2)
        ->and($data->partners[0]->nome)->toBe('FULANO DE TAL')
        ->and($data->partners[0]->documento)->toBe('***123456**')
        ->and($data->partners[0]->qualificacao)->toBe('Sócio-Administrador')
        ->and($data->raw)->toBe(brasilApiPayload());

    Http::assertSent(fn ($request) => str_contains($request->url(), '/11222333000181'));
});

it('returns null when the company is not found (404)', function () {
    Http::fake([BASE_URL.'/*' => Http::response(['message' => 'CNPJ não encontrado'], 404)]);

    expect(makeProvider()->fetch('11222333000181'))->toBeNull();
});

it('throws on an invalid CNPJ without hitting the network', function () {
    Http::fake();

    expect(fn () => makeProvider()->fetch('123'))->toThrow(InvalidCnpjException::class);

    Http::assertNothingSent();
});

it('retries on HTTP 429 and then succeeds', function () {
    Http::fake([BASE_URL.'/*' => Http::sequence()
        ->push(['message' => 'rate limited'], 429)
        ->push(brasilApiPayload(), 200),
    ]);

    $data = makeProvider(tries: 3)->fetch('11222333000181');

    expect($data)->toBeInstanceOf(CompanyData::class)
        ->and($data->razaoSocial)->toBe('EMPRESA EXEMPLO LTDA');

    Http::assertSentCount(2);
});

it('throws a provider exception after exhausting retries on 5xx', function () {
    Http::fake([BASE_URL.'/*' => Http::response('erro interno', 500)]);

    expect(fn () => makeProvider(tries: 2)->fetch('11222333000181'))
        ->toThrow(CnpjProviderException::class);

    Http::assertSentCount(2);
});

it('throttles requests once the per-minute cap is reached', function () {
    Sleep::fake();
    Http::fake([BASE_URL.'/*' => Http::response(brasilApiPayload(), 200)]);

    $provider = makeProvider(throttlePerMinute: 2);
    $provider->fetch('11222333000181'); // hit #1
    $provider->fetch('11222333000181'); // hit #2 (at cap, no sleep yet)
    $provider->fetch('11222333000181'); // over cap -> sleeps once

    Sleep::assertSleptTimes(1);
});
