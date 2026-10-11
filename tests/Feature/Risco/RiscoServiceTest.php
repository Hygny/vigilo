<?php

declare(strict_types=1);

use App\Enums\FonteSancao;
use App\Services\Risco\PortalTransparenciaClient;
use App\Services\Risco\RiscoService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

/** Serviço com um cliente real apontado para a base fake do Http::fake. */
function makeRiscoService(?string $apiKey = 'chave-teste', int $ttlMinutes = 720): RiscoService
{
    return new RiscoService(
        new PortalTransparenciaClient(
            baseUrl: 'https://api.portaldatransparencia.gov.br/api-de-dados',
            apiKey: $apiKey,
            timeout: 10,
        ),
        cacheTtlMinutes: $ttlMinutes,
    );
}

it('degrades to "not configured" without an API key (no HTTP call)', function () {
    Http::fake();

    $report = makeRiscoService(apiKey: null)->para('11222333000181');

    expect($report->configurado)->toBeFalse()
        ->and($report->disponivel)->toBeFalse()
        ->and($report->sancoes)->toBe([]);

    Http::assertNothingSent();
});

it('maps sanctions and ties them to the queried CNPJ, dropping other companies', function () {
    Http::fake(function (Request $request) {
        $url = $request->url();

        if (str_contains($url, '/ceis')) {
            return Http::response([
                [ // este CNPJ → entra
                    'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
                    'tipoSancao' => ['descricaoResumida' => 'Inidoneidade'],
                    'orgaoSancionador' => ['nome' => 'TCU'],
                    'dataInicioSancao' => '01/03/2023',
                    'dataFimSancao' => '01/03/2025',
                    'fundamentacao' => [['descricao' => 'Art. 46 da Lei 8.443/92']],
                ],
                [ // OUTRO CNPJ (filtro furado da API) → descartado
                    'pessoa' => ['cnpjFormatado' => '99.999.999/0001-99'],
                    'tipoSancao' => ['descricaoResumida' => 'Não é desta empresa'],
                ],
            ]);
        }

        if (str_contains($url, '/cnep')) {
            return Http::response([[
                'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
                'tipoSancao' => ['descricaoResumida' => 'Multa (Lei Anticorrupção)'],
                'orgaoSancionador' => ['nome' => 'CGU'],
                'dataInicioSancao' => '10/01/2024',
            ]]);
        }

        return Http::response([]); // cepim vazio
    });

    $report = makeRiscoService()->para('11.222.333/0001-81');

    expect($report->configurado)->toBeTrue()
        ->and($report->disponivel)->toBeTrue()
        ->and($report->total())->toBe(2); // 1 CEIS + 1 CNEP; o CNPJ alheio foi cortado

    $ceis = collect($report->sancoes)->firstWhere(fn ($s) => $s->fonte === FonteSancao::Ceis);

    expect($ceis->tipo)->toBe('Inidoneidade')
        ->and($ceis->orgao)->toBe('TCU')
        ->and($ceis->dataInicio)->toBe('2023-03-01')
        ->and($ceis->dataFim)->toBe('2025-03-01')
        ->and($ceis->fundamentacao)->toBe('Art. 46 da Lei 8.443/92');
});

it('uses codigoSancionado for CEIS/CNEP and cnpjSancionado for CEPIM', function () {
    Http::fake(fn () => Http::response([]));

    makeRiscoService()->para('11222333000181');

    Http::assertSent(fn (Request $r) => str_contains($r->url(), '/ceis') && str_contains($r->url(), 'codigoSancionado=11222333000181'));
    Http::assertSent(fn (Request $r) => str_contains($r->url(), '/cnep') && str_contains($r->url(), 'codigoSancionado=11222333000181'));
    Http::assertSent(fn (Request $r) => str_contains($r->url(), '/cepim') && str_contains($r->url(), 'cnpjSancionado=11222333000181'));
});

it('matches a CEPIM record through its own CNPJ path and fields', function () {
    Http::fake(function (Request $request) {
        if (str_contains($request->url(), '/cepim')) {
            return Http::response([[
                'pessoaJuridica' => ['cnpjFormatado' => '11.222.333/0001-81'], // caminho alternativo de CNPJ
                'motivo' => 'Convênio irregular',
                'orgaoSuperior' => ['nome' => 'Ministério da Cidadania'],
                'dataReferencia' => '05/06/2022',
            ]]);
        }

        return Http::response([]);
    });

    $report = makeRiscoService()->para('11222333000181');

    expect($report->total())->toBe(1);

    $cepim = $report->sancoes[0];

    expect($cepim->fonte)->toBe(FonteSancao::Cepim)
        ->and($cepim->tipo)->toBe('Convênio irregular')
        ->and($cepim->orgao)->toBe('Ministério da Cidadania')
        ->and($cepim->dataInicio)->toBe('2022-06-05');
});

it('returns no sanctions when the API returns only other CNPJs (unreliable filter)', function () {
    Http::fake(fn () => Http::response([
        ['pessoa' => ['cnpjFormatado' => '99.999.999/0001-99'], 'tipoSancao' => ['descricaoResumida' => 'x']],
    ]));

    $report = makeRiscoService()->para('11222333000181');

    expect($report->disponivel)->toBeTrue()
        ->and($report->temSancoes())->toBeFalse();
});

it('marks the report unavailable when a source fails', function () {
    Http::fake(fn () => Http::response('erro interno', 500));

    $report = makeRiscoService()->para('11222333000181');

    expect($report->configurado)->toBeTrue()
        ->and($report->disponivel)->toBeFalse();
});

it('caches a good result (second lookup does not hit the API)', function () {
    Http::fake(fn () => Http::response([]));

    $service = makeRiscoService();
    $service->para('11222333000181');
    $service->para('11222333000181');

    Http::assertSentCount(3); // 3 fontes na 1ª consulta; a 2ª veio do cache
});

it('does not cache a failure (retries on the next lookup)', function () {
    Http::fake(fn () => Http::response('erro', 500));

    $service = makeRiscoService();
    $service->para('11222333000181');
    $service->para('11222333000181');

    Http::assertSentCount(2); // cada consulta re-tentou a 1ª fonte (CEIS)
});
