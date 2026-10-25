<?php

declare(strict_types=1);

use App\Services\Risco\PgfnLookup;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    useCnpjSqlite();
    createPgfnTables();
});

it('sums every inscription of the company by the CNPJ root (matriz + filiais)', function () {
    seedPgfnDivida('11222333000181', 1000.00);
    seedPgfnDivida('11222333000262', 234.56, ajuizado: true, tipo: 'previdenciario'); // filial
    seedPgfnDivida('99888777000166', 5.00); // outra empresa

    $summary = app(PgfnLookup::class)->para('11.222.333/0002-62'); // consulta pela filial

    expect($summary)->not->toBeNull()
        ->and($summary->inscricoes)->toBe(2)
        ->and($summary->ajuizadas)->toBe(1)
        ->and($summary->total)->toEqualWithDelta(1234.56, 0.001)
        ->and($summary->resumo())->toBe('2 inscrições · R$ 1.234,56');
});

it('returns null when the company has no inscription, or for an invalid CNPJ', function () {
    seedPgfnDivida('99888777000166', 5.00);

    expect(app(PgfnLookup::class)->para('11222333000181'))->toBeNull()
        ->and(app(PgfnLookup::class)->para('123'))->toBeNull();
});

it('summarizes many roots in one query and only returns the ones with debt', function () {
    seedPgfnDivida('11222333000181', 10.00);
    seedPgfnDivida('44555666000199', 20.00);
    seedPgfnDivida('44555666000199', 30.00);

    $queries = 0;
    DB::connection('cnpj')->listen(function () use (&$queries): void {
        $queries++;
    });

    $resumos = app(PgfnLookup::class)->paraBasicos(['11222333', '44555666', '77777777', 'lixo', '11222333']);

    expect(array_keys($resumos))->toEqualCanonicalizing(['11222333', '44555666'])
        ->and($resumos['44555666']->inscricoes)->toBe(2)
        ->and($resumos['44555666']->total)->toEqualWithDelta(50.0, 0.001)
        ->and($queries)->toBe(1);
});

it('does not query at all for an empty list of roots', function () {
    expect(app(PgfnLookup::class)->paraBasicos([]))->toBe([]);
});

it('reports the loaded PGFN quarter', function () {
    expect(app(PgfnLookup::class)->referencia())->toBeNull();

    DB::connection('cnpj')->table('pgfn_cargas')->insert([
        ['trimestre' => '2026_trimestre_02', 'inscricoes' => 10, 'carregado_em' => '2026-07-10 03:00:00'],
        ['trimestre' => '2026_trimestre_03', 'inscricoes' => 12, 'carregado_em' => '2026-10-25 20:00:00'],
    ]);

    expect(app(PgfnLookup::class)->referencia())->toBe('2026_trimestre_03');
});

it('throws (never answers "no debt") when the PGFN table is unavailable', function () {
    DB::connection('cnpj')->getSchemaBuilder()->drop('pgfn_dividas');

    app(PgfnLookup::class)->para('11222333000181');
})->throws(QueryException::class);
