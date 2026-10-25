<?php

declare(strict_types=1);

use App\Support\BuscaNome;

// Mesmos exemplos do tests/deploy/cnpj-db-sql.test.sh (busca_norm no Postgres):
// as duas normalizações precisam produzir a MESMA chave.
it('normalizes exactly like busca_norm() in Postgres', function (?string $entrada, string $chave) {
    expect(BuscaNome::chave($entrada))->toBe($chave);
})->with([
    'acento e caixa' => ['Padaria São José Ltda', ' PADARIA SAO JOSE LTDA '],
    'pontuação vira separador' => ['J.NOVAIS & CIA', ' J NOVAIS CIA '],
    'CPF no fim da razão de MEI' => ['JOSÉ DA SILVA 12345678901', ' JOSE DA SILVA '],
    'nulo' => [null, '  '],
    '12 dígitos no fim não é CPF' => ['EMPRESA 123456789012', ' EMPRESA 123456789012 '],
    'só o CPF' => ['12345678901', '  '],
]);

it('extracts search terms, dropping 1-letter words and CPF-like numbers', function () {
    expect(BuscaNome::termos('  Padaria  são   josé e filhos '))->toBe(['PADARIA', 'SAO', 'JOSE', 'FILHOS'])
        ->and(BuscaNome::termos('joao 12345678901 silva'))->toBe(['JOAO', 'SILVA'])
        ->and(BuscaNome::termos('padaria padaria'))->toBe(['PADARIA']);
});

it('requires at least one term with 3+ characters', function () {
    expect(BuscaNome::termos('jr sa'))->toBe([])
        ->and(BuscaNome::termos('a'))->toBe([])
        ->and(BuscaNome::termos('ab ltda'))->toBe(['AB', 'LTDA']);
});

it('caps the number of terms', function () {
    expect(BuscaNome::termos('um dois tres quatro cinco seis sete oito'))->toHaveCount(BuscaNome::MAX_TERMOS);
});

it('detects a CPF typed in the search box', function () {
    expect(BuscaNome::pareceCpf('123.456.789-01'))->toBeTrue()
        ->and(BuscaNome::pareceCpf('12345678901'))->toBeTrue()
        ->and(BuscaNome::pareceCpf('padaria 12345678901'))->toBeFalse()
        ->and(BuscaNome::pareceCpf('11.222.333/0001-81'))->toBeFalse();
});
