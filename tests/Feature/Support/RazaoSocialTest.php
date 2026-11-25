<?php

declare(strict_types=1);

use App\Support\PorteEmpresa;
use App\Support\RazaoSocial;

it('masks the full CPF at the end of a MEI company name', function () {
    expect(RazaoSocial::exibir('JOSE DA SILVA 12345678901'))->toBe('JOSE DA SILVA ***456789**')
        ->and(RazaoSocial::exibir('  MARIA SOUZA 98765432100  '))->toBe('MARIA SOUZA ***654321**');
});

it('leaves ordinary names and other numbers alone', function () {
    expect(RazaoSocial::exibir('PADARIA 2000 LTDA'))->toBe('PADARIA 2000 LTDA')
        ->and(RazaoSocial::exibir('EMPRESA 123456789012'))->toBe('EMPRESA 123456789012') // 12 dígitos: não é CPF
        ->and(RazaoSocial::exibir(null))->toBeNull();
});

it('translates the Receita porte code', function () {
    expect(PorteEmpresa::label('01'))->toBe('ME')
        ->and(PorteEmpresa::label('05'))->toBe('DEMAIS')
        ->and(PorteEmpresa::label('00'))->toBeNull()
        ->and(PorteEmpresa::label(null))->toBeNull();
});

it('drops the trailing CPF (full or masked) for what leaves the platform', function (?string $razao, ?string $esperado) {
    expect(RazaoSocial::semDocumento($razao))->toBe($esperado);
})->with([
    'MEI com CPF íntegro' => ['MARIA SOUZA 12345678901', 'MARIA SOUZA'],
    'já mascarado' => ['MARIA SOUZA ***456789**', 'MARIA SOUZA'],
    'empresa comum' => ['PADARIA AURORA LTDA', 'PADARIA AURORA LTDA'],
    'número no meio fica' => ['LOJA 12345678901 CENTRO', 'LOJA 12345678901 CENTRO'],
    'só o documento' => ['12345678901', null],
    'nulo' => [null, null],
]);
