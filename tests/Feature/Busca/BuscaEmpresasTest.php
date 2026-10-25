<?php

declare(strict_types=1);

use App\Services\Vigilancia\BuscaEmpresas;

/** @return list<string> CNPJs encontrados, na ordem do ranking */
function buscarCnpjs(string $consulta, ?string $uf = null, ?string $situacao = null, int $candidatos = 200): array
{
    $resultado = (new BuscaEmpresas('cnpj', $candidatos))->buscar($consulta, $uf, $situacao);

    return array_map(fn ($r) => $r->cnpj, $resultado->itens);
}

it('finds companies by the start of words in the company name, ignoring accents', function () {
    seedBuscaBase([
        ['11222333000181', 'PADARIA SÃO JOSÉ LTDA', null, '02', 'SP'],
        ['44555666000107', 'MERCADO JOSELITO LTDA', null, '02', 'SP'],   // "JOSE" no começo de JOSELITO casa
        ['77888999000155', 'ARTEJOSE MOVEIS LTDA', null, '02', 'SP'],    // JOSE no MEIO da palavra: não casa
    ]);

    expect(buscarCnpjs('jose'))->toEqualCanonicalizing(['11222333000181', '44555666000107'])
        ->and(buscarCnpjs('padaria são josé'))->toBe(['11222333000181']);
});

it('finds an establishment by its trade name and returns that establishment', function () {
    seedBuscaBase([
        ['11222333000181', 'COMERCIAL XYZ LTDA', 'LOJA CENTRO', '02', 'SP'],
        ['11222333000262', 'COMERCIAL XYZ LTDA', 'PAO QUENTE PINHEIROS', '02', 'SP', false],
    ]);

    $resultado = (new BuscaEmpresas('cnpj'))->buscar('pão quente');

    expect($resultado->itens)->toHaveCount(1)
        ->and($resultado->itens[0]->cnpj)->toBe('11222333000262')
        ->and($resultado->itens[0]->matriz)->toBeFalse()
        ->and($resultado->itens[0]->casouPor)->toBe('fantasia')
        ->and($resultado->itens[0]->razaoSocial)->toBe('COMERCIAL XYZ LTDA')
        ->and($resultado->itens[0]->municipio)->toBe('SAO PAULO');
});

it('returns the company once when both the name and the trade name match', function () {
    seedBuscaBase([['11222333000181', 'PADARIA AURORA LTDA', 'PADARIA AURORA', '02', 'SP']]);

    expect(buscarCnpjs('aurora'))->toBe(['11222333000181']);
});

it('ranks whole-word and phrase matches first, active companies above', function () {
    seedBuscaBase([
        ['11111111000111', 'JOAO SILVANO REPRESENTACOES', null, '02', 'SP'],  // só prefixo de SILVA
        ['22222222000122', 'SILVA E JOAO COMERCIO', null, '02', 'SP'],        // palavras inteiras, fora de ordem
        ['33333333000133', 'JOAO SILVA COMERCIO', null, '08', 'SP'],          // frase exata, BAIXADA
        ['44444444000144', 'JOAO SILVA MERCEARIA', null, '02', 'SP'],         // frase exata, ATIVA
    ]);

    expect(buscarCnpjs('joao silva'))->toBe(['44444444000144', '33333333000133', '22222222000122', '11111111000111']);
});

it('puts the head office ahead of a branch whose shorter trade name ties on relevance', function () {
    seedBuscaBase([
        ['11222333000181', 'MAGAZINE LUIZA S/A', null, '02', 'SP'],
        ['11222333000262', 'MAGAZINE LUIZA S/A', 'MAGAZINE LUIZA', '02', 'SP', false],
    ]);

    expect(buscarCnpjs('magazine luiza'))->toBe(['11222333000181', '11222333000262']);
});

it('filters by UF and by situação', function () {
    seedBuscaBase([
        ['11111111000111', 'AURORA ALIMENTOS', null, '02', 'SP'],
        ['22222222000122', 'AURORA ALIMENTOS RJ', null, '02', 'RJ'],
        ['33333333000133', 'AURORA ALIMENTOS ANTIGA', null, '08', 'SP'],
    ]);

    expect(buscarCnpjs('aurora', uf: 'RJ'))->toBe(['22222222000122'])
        ->and(buscarCnpjs('aurora', uf: 'SP', situacao: '08'))->toBe(['33333333000133']);
});

it('masks the CPF in a MEI company name and cannot be searched by CPF', function () {
    seedBuscaBase([['11222333000181', 'MARIA SOUZA 12345678901', null, '02', 'SP']]);

    $resultado = (new BuscaEmpresas('cnpj'))->buscar('maria souza');

    expect($resultado->itens[0]->razaoSocial)->toBe('MARIA SOUZA ***456789**')
        ->and(buscarCnpjs('12345678901 maria'))->toBe(['11222333000181']) // o número é descartado, "maria" busca
        ->and(buscarCnpjs('12345678901'))->toBe([]);
});

it('does not let LIKE wildcards typed by the user widen the search', function () {
    seedBuscaBase([['11222333000181', 'PADARIA AURORA', null, '02', 'SP']]);

    expect(buscarCnpjs('%%% ___'))->toBe([])
        ->and(buscarCnpjs('aur%ora'))->toBe([]); // vira "AUR ORA": não é começo de palavra de nada
});

it('flags a truncated result when a source fills the candidate cap', function () {
    seedBuscaBase([
        ['11111111000111', 'AURORA UM', null, '02', 'SP'],
        ['22222222000122', 'AURORA DOIS', null, '02', 'SP'],
        ['33333333000133', 'AURORA TRES', null, '02', 'SP'],
    ]);

    $cheio = (new BuscaEmpresas('cnpj', candidatos: 2))->buscar('aurora');
    $folga = (new BuscaEmpresas('cnpj', candidatos: 10))->buscar('aurora');

    expect($cheio->truncado)->toBeTrue()
        ->and($cheio->itens)->toHaveCount(2)
        ->and($folga->truncado)->toBeFalse()
        ->and($folga->itens)->toHaveCount(3);
});

it('flags a truncated result when more matches than the display limit remain', function () {
    seedBuscaBase([
        ['11111111000111', 'AURORA UM', null, '02', 'SP'],
        ['22222222000122', 'AURORA DOIS', null, '02', 'SP'],
        ['33333333000133', 'AURORA TRES', null, '02', 'SP'],
    ]);

    $cortado = (new BuscaEmpresas('cnpj'))->buscar('aurora', limite: 2);
    $inteiro = (new BuscaEmpresas('cnpj'))->buscar('aurora', limite: 3);

    expect($cortado->itens)->toHaveCount(2)
        ->and($cortado->truncado)->toBeTrue()
        ->and($inteiro->truncado)->toBeFalse();
});

it('returns nothing (without querying) when no term is long enough', function () {
    useCnpjSqlite(); // sem tabelas: se consultasse, lançaria

    $resultado = (new BuscaEmpresas('cnpj'))->buscar('ab c');

    expect($resultado->itens)->toBe([])->and($resultado->termos)->toBe([]);
});
