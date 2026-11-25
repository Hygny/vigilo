<?php

declare(strict_types=1);

use App\DTO\Contratos\ContratoPublico;
use App\DTO\Risco\RiscoReport;
use App\DTO\Risco\Sancao;
use App\Enums\FonteSancao;
use App\Livewire\Companies\Show;
use App\Livewire\Empresas\ContratosPublicos as ContratosCard;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Services\Contratos\ContratosPublicos;
use App\Services\Risco\PortalTransparenciaClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Livewire;

/** Um contrato como a API do Portal devolve (contratos/cpf-cnpj). */
function contratoApi(int $id, string $assinatura, string $fim, float $valor, string $cnpjFornecedor = '11.222.333/0001-81', string $objeto = 'Objeto: Fornecimento de pão'): array
{
    return [
        'id' => $id, 'numero' => (string) $id, 'objeto' => $objeto, 'situacaoContrato' => 'Publicado', 'modalidadeCompra' => 'Pregão',
        'dataAssinatura' => $assinatura, 'dataInicioVigencia' => $assinatura, 'dataFimVigencia' => $fim,
        'valorInicialCompra' => $valor, 'valorFinalCompra' => $valor,
        'unidadeGestora' => ['nome' => 'UG X', 'orgaoVinculado' => ['sigla' => 'MS', 'nome' => 'Ministério da Saúde', 'cnpj' => '00394544000185']],
        'fornecedor' => ['cnpjFormatado' => $cnpjFornecedor, 'nome' => 'PADARIA AURORA'],
        'compra' => ['numero' => '1', 'contatoResponsavel' => 'FULANO SERVIDOR PUBLICO'],
    ];
}

/**
 * Fake da API: `$paginas[n]` = registros da página n (ausente = vazia).
 *
 * @param  array<int, list<array<string, mixed>>>  $paginas
 */
function fakePortalContratos(array $paginas): void
{
    Http::fake(function (Request $request) use ($paginas) {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $q);

        return Http::response($paginas[(int) ($q['pagina'] ?? 1)] ?? []);
    });
}

beforeEach(function (): void {
    config()->set('risco.portal.api_key', 'chave-teste');
    Cache::flush();
});

it('reads every page until the last, keeps only the company as supplier and sorts the newest first', function () {
    $p1 = array_map(fn (int $i) => contratoApi($i, '2015-01-'.str_pad((string) (($i % 28) + 1), 2, '0', STR_PAD_LEFT), '2016-01-01', 100.0), range(1, 14));
    $p1[] = contratoApi(99, '2026-01-01', '2026-12-31', 5000.0, '99.888.777/0001-66'); // outro fornecedor: fora
    fakePortalContratos([1 => $p1, 2 => [contratoApi(200, '2025-06-01', '2099-01-01', 1000.0), contratoApi(201, '2010-01-01', '2011-01-01', 10.0)]]);

    $r = app(ContratosPublicos::class)->para('11222333000181');

    expect($r->disponivel)->toBeTrue()
        ->and($r->total())->toBe(16)
        ->and($r->truncado)->toBeFalse()
        ->and($r->contratos[0]->id)->toBe(200) // mais recente primeiro
        ->and($r->contratos[0]->vigente())->toBeTrue()
        ->and($r->contratos[0]->objeto)->toBe('Fornecimento de pão') // sem o prefixo "Objeto:"
        ->and($r->contratos[0]->orgao)->toBe('Ministério da Saúde')
        ->and($r->contratos[0]->url())->toBe('https://portaldatransparencia.gov.br/contratos/200')
        ->and($r->vigentes())->toBe(1)
        ->and($r->valorTotal())->toEqualWithDelta(14 * 100 + 1000 + 10, 0.001);

    Http::assertSent(fn (Request $req) => str_contains($req->url(), 'contratos/cpf-cnpj') && str_contains($req->url(), 'cpfCnpj=11222333000181')
        && $req->hasHeader('chave-api-dados', 'chave-teste'));
});

it('marks the result as truncated when every page up to the ceiling is full', function () {
    config()->set('risco.contratos.max_paginas', 2);
    app()->forgetInstance(ContratosPublicos::class);
    $cheia = fn (int $base) => array_map(fn (int $i) => contratoApi($base + $i, '2020-01-01', '2021-01-01', 1.0), range(1, 15));
    fakePortalContratos([1 => $cheia(0), 2 => $cheia(100), 3 => $cheia(200)]);

    $r = app(ContratosPublicos::class)->para('11222333000181');

    expect($r->total())->toBe(30)->and($r->truncado)->toBeTrue();
    Http::assertSentCount(2);
});

it('caches only a good result and says "unavailable" (never "no contracts") when the API fails', function () {
    // Um fake só (o Http::fake acumula callbacks): a API falha e depois volta.
    $falhando = true;
    Http::fake(function () use (&$falhando) {
        return $falhando ? Http::response(['erro' => 'x'], 500) : Http::response([contratoApi(1, '2024-01-01', '2025-01-01', 10.0)]);
    });
    $servico = app(ContratosPublicos::class);

    $r = $servico->para('11222333000181');
    expect($r->disponivel)->toBeFalse()->and($r->configurado)->toBeTrue();

    $falhando = false;
    $this->travel(6)->minutes(); // passa a janela curta de falha
    expect($servico->para('11222333000181')->total())->toBe(1); // tentou de novo (falha não foi para o cache)
    Http::assertSentCount(2); // empresa comum: 1 página por consulta (a 1ª vai sozinha)
    expect($servico->para('11222333000181')->total())->toBe(1); // agora do cache
    Http::assertSentCount(2);
});

it('does not call the API without the key', function () {
    config()->set('risco.portal.api_key', null);
    app()->forgetInstance(PortalTransparenciaClient::class);
    app()->forgetInstance(ContratosPublicos::class);
    Http::fake();

    expect(app(ContratosPublicos::class)->para('11222333000181')->configurado)->toBeFalse();
    Http::assertNothingSent();
});

it('renders the card with summary, links and no public servant name', function () {
    fakePortalContratos([1 => [contratoApi(200, '2025-06-01', '2099-01-01', 1234.5), contratoApi(201, '2010-01-01', '2011-01-01', 10.0)]]);
    $user = User::factory()->for(Organization::factory())->create();

    Livewire::actingAs($user)->test(ContratosCard::class, ['cnpj' => '11222333000181'])
        ->assertSee('Contratos públicos')
        ->assertSee('2 contrato(s) · R$ 1.244,50 · 1 vigente(s)')
        ->assertSee('Fornecimento de pão')
        ->assertSee('MS — Ministério da Saúde')
        ->assertSee('vigente')
        ->assertSee('https://portaldatransparencia.gov.br/contratos/200', false)
        ->assertSee('https://pncp.gov.br/app/contratos?q=11222333000181', false)
        ->assertDontSee('FULANO SERVIDOR PUBLICO');
});

it('shows the "no federal contract" state', function () {
    fakePortalContratos([]);

    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(ContratosCard::class, ['cnpj' => '11222333000181'])
        ->assertSee('Nenhum contrato do Poder Executivo Federal');
});

it('shows the "unavailable" state, never "no contracts", when the API fails', function () {
    $user = User::factory()->for(Organization::factory())->create();
    Http::fake(fn () => Http::response('', 503));
    Livewire::actingAs($user)->test(ContratosCard::class, ['cnpj' => '11444777000161'])
        ->assertSee('Não foi possível consultar os contratos agora')
        ->assertDontSee('Nenhum contrato do Poder Executivo Federal');
});

it('403s without organization and 404s an invalid CNPJ', function () {
    Http::fake();
    Livewire::actingAs(User::factory()->create(['organization_id' => null]))->test(ContratosCard::class, ['cnpj' => '11222333000181'])->assertForbidden();
    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(ContratosCard::class, ['cnpj' => '11222333000182'])->assertNotFound();
    Http::assertNothingSent();
});

it('mounts the card lazily on the portfolio company page', function () {
    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    Livewire::actingAs(User::factory()->for($org)->create())->test(Show::class, ['company' => $company])
        ->assertSeeLivewire(ContratosCard::class);
});

it('shortens a long contract object at a word boundary', function () {
    $longo = 'Objeto: '.Str::repeat('abcdefghij ', 30); // 11 caracteres por palavra: 160 cairia no meio de uma
    fakePortalContratos([1 => [contratoApi(1, '2024-01-01', '2025-01-01', 1.0, objeto: $longo)]]);

    $html = Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(ContratosCard::class, ['cnpj' => '11222333000181'])->html();

    expect($html)->toContain('abcdefghij...')
        ->not->toMatch('/abcdefghij [a-i]+\.\.\./'); // nenhum pedaço de palavra antes das reticências
});

it('really hits the cache with a SERIALIZING store (as Redis in production) — contracts and sanctions', function () {
    config()->set('cache.stores.array.serialize', true);
    app('cache')->forgetDriver('array');
    fakePortalContratos([1 => [contratoApi(1, '2024-01-01', '2099-01-01', 10.0)]]);
    $servico = app(ContratosPublicos::class);

    expect($servico->para('11222333000181')->total())->toBe(1);
    expect($servico->para('11222333000181')->contratos[0])->toBeInstanceOf(ContratoPublico::class); // do cache, desserializado
    Http::assertSentCount(1);

    // O relatório de sanções (RiscoService) passa pelo mesmo caminho.
    $rel = RiscoReport::ok('11222333000181', [new Sancao(FonteSancao::Ceis, 'Inidônea', 'CGU', '2024-01-01', null, null)]);
    Cache::put('teste-risco', $rel, 60);
    expect(Cache::get('teste-risco'))->toBeInstanceOf(RiscoReport::class)
        ->and(Cache::get('teste-risco')->sancoes[0]->fonte)->toBe(FonteSancao::Ceis);

    // Qualquer outra classe continua bloqueada (proteção contra gadget chain).
    Cache::put('teste-outra', new ArrayObject([1]), 60);
    expect(Cache::get('teste-outra'))->not->toBeInstanceOf(ArrayObject::class);
});

it('says "unavailable" when a later page of a batch fails, and waits a short window before re-reading', function () {
    $cheia = array_map(fn (int $i) => contratoApi($i, '2020-01-01', '2021-01-01', 1.0), range(1, 15));
    Http::fake(function (Request $request) use ($cheia) {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $q);

        return match ((int) ($q['pagina'] ?? 1)) {
            1, 2 => Http::response($cheia),
            3 => Http::response('limite', 429),
            default => Http::response([]),
        };
    });
    $servico = app(ContratosPublicos::class);

    expect($servico->para('11222333000181')->disponivel)->toBeFalse();
    $enviadas = count(Http::recorded());

    expect($servico->para('11222333000181')->disponivel)->toBeFalse(); // janela de falha: não relê
    expect(count(Http::recorded()))->toBe($enviadas);

    $this->travel(6)->minutes();
    $servico->para('11222333000181');
    expect(count(Http::recorded()))->toBeGreaterThan($enviadas); // passou a janela: tenta de novo
});

it('treats a connection error inside the parallel batch as unavailable', function () {
    $cheia = array_map(fn (int $i) => contratoApi($i, '2020-01-01', '2021-01-01', 1.0), range(1, 15));
    Http::fake(function (Request $request) use ($cheia) {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $q);

        if ((int) ($q['pagina'] ?? 1) === 1) {
            return Http::response($cheia);
        }

        throw new ConnectionException('cURL error 28: timeout');
    });

    expect(app(ContratosPublicos::class)->para('11222333000181')->disponivel)->toBeFalse();
});

it('dedups the same contract returned on two pages', function () {
    $p1 = array_map(fn (int $i) => contratoApi($i, '2020-01-01', '2021-01-01', 1.0), range(1, 15));
    fakePortalContratos([1 => $p1, 2 => [contratoApi(15, '2020-01-01', '2021-01-01', 1.0), contratoApi(16, '2020-01-01', '2021-01-01', 1.0)]]);

    expect(app(ContratosPublicos::class)->para('11222333000181')->total())->toBe(16);
});

it('gives up as "unavailable" when the total reading deadline passes', function () {
    $cheia = array_map(fn (int $i) => contratoApi($i, '2020-01-01', '2021-01-01', 1.0), range(1, 15));
    fakePortalContratos([1 => $cheia, 2 => $cheia]);
    $servico = new ContratosPublicos(app(PortalTransparenciaClient::class), maxPaginas: 40, prazoSegundos: -1);

    expect($servico->para('11222333000181')->disponivel)->toBeFalse();
    Http::assertSentCount(1); // só a 1ª página: o prazo barrou o lote seguinte
});

it('has its own loading placeholder (not the risk one)', function () {
    $html = (new ContratosCard)->placeholder()->render();

    expect($html)->toContain('Contratos públicos')->not->toContain('CEIS');
});

it('renders the truncated state honestly (at least N, link to the Portal page)', function () {
    config()->set('risco.contratos.max_paginas', 1);
    app()->forgetInstance(ContratosPublicos::class);
    fakePortalContratos([1 => array_map(fn (int $i) => contratoApi($i, '2020-01-01', '2021-01-01', 1.0), range(1, 15))]);

    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(ContratosCard::class, ['cnpj' => '11222333000181'])
        ->assertSee('ao menos 15 contrato(s)')
        ->assertSee('pode haver mais contratos, inclusive mais recentes')
        ->assertSee('https://portaldatransparencia.gov.br/pessoa-juridica/11222333000181', false);
});

it('says "showing the N most recent of M" when there are more than the card lists', function () {
    $p1 = array_map(fn (int $i) => contratoApi($i, '2020-01-'.str_pad((string) ($i % 28 + 1), 2, '0', STR_PAD_LEFT), '2021-01-01', 1.0), range(1, 15));
    $p2 = array_map(fn (int $i) => contratoApi(100 + $i, '2019-01-01', '2020-01-01', 1.0), range(1, 10));
    fakePortalContratos([1 => $p1, 2 => $p2]);

    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(ContratosCard::class, ['cnpj' => '11222333000181'])
        ->assertSee('Mostrando os 20 mais recentes de 25.')
        ->assertDontSee('ao menos');
});

it('does not mark as "vigente" a contract whose situation says it ended early', function () {
    $rescindido = array_merge(contratoApi(1, '2024-01-01', '2099-01-01', 1.0), ['situacaoContrato' => 'Rescindido']);
    fakePortalContratos([1 => [$rescindido]]);

    $r = app(ContratosPublicos::class)->para('11222333000181');

    expect($r->contratos[0]->vigente())->toBeFalse()
        ->and($r->contratos[0]->detalhe())->toContain('Rescindido')
        ->and($r->vigentes())->toBe(0);
});

it('treats a 200 answer that is not a list as unavailable (not "no contracts")', function () {
    Http::fake(fn () => Http::response(['mensagem' => 'Serviço em manutenção']));

    expect(app(ContratosPublicos::class)->para('11222333000181')->disponivel)->toBeFalse();
});
