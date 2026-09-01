<?php

declare(strict_types=1);

use App\Support\Cnpj;
use App\Support\Exceptions\InvalidCnpjException;

it('normalizes formatted CNPJs to 14 digits', function () {
    expect(Cnpj::normalize('11.222.333/0001-81'))->toBe('11222333000181')
        ->and(Cnpj::normalize('11222333000181'))->toBe('11222333000181');
});

it('left-pads CNPJs whose leading zeros were dropped', function () {
    // Spreadsheets frequently strip leading zeros from CNPJs stored as numbers.
    expect(Cnpj::normalize('123'))->toBe('00000000000123')
        ->and(strlen(Cnpj::normalize('123')))->toBe(14);
});

it('accepts a valid CNPJ', function () {
    expect(Cnpj::isValid('11222333000181'))->toBeTrue()
        ->and(Cnpj::isValid('11.222.333/0001-81'))->toBeTrue();
});

it('rejects a CNPJ with a wrong check digit', function () {
    expect(Cnpj::isValid('11222333000180'))->toBeFalse() // wrong 2nd DV
        ->and(Cnpj::isValid('11222333000191'))->toBeFalse(); // wrong 1st DV
});

it('rejects sequences of a single repeated digit', function (string $cnpj) {
    expect(Cnpj::isValid($cnpj))->toBeFalse();
})->with([
    '00000000000000',
    '11111111111111',
    '99999999999999',
]);

it('rejects values that are not 14 digits', function (string $cnpj) {
    expect(Cnpj::isValid($cnpj))->toBeFalse();
})->with([
    '',
    'abc',
    '1122233300018',        // 13 digits, pads then fails DV
    '112223330001810',      // 15 digits
]);

it('builds a value object from a valid string', function () {
    $cnpj = Cnpj::fromString('11.222.333/0001-81');

    expect($cnpj->value)->toBe('11222333000181')
        ->and((string) $cnpj)->toBe('11222333000181')
        ->and($cnpj->formatted())->toBe('11.222.333/0001-81');
});

it('throws when building from an invalid string', function () {
    Cnpj::fromString('11222333000180');
})->throws(InvalidCnpjException::class);

it('tryFrom returns a value object or null', function () {
    expect(Cnpj::tryFrom('11222333000181'))->toBeInstanceOf(Cnpj::class)
        ->and(Cnpj::tryFrom('11222333000180'))->toBeNull();
});
