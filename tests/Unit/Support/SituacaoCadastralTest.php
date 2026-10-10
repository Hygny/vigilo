<?php

declare(strict_types=1);

use App\Support\SituacaoCadastral;

it('maps codes to labels and back', function () {
    expect(SituacaoCadastral::label('02'))->toBe('ATIVA')
        ->and(SituacaoCadastral::label('08'))->toBe('BAIXADA')
        ->and(SituacaoCadastral::label('99'))->toBe('')
        ->and(SituacaoCadastral::code('BAIXADA'))->toBe('08')
        ->and(SituacaoCadastral::code('inexistente'))->toBeNull();
});

it('derives the negative set from the map (everything but ATIVA)', function () {
    $negatives = SituacaoCadastral::negativeLabels();

    expect($negatives)->toContain('NULA', 'SUSPENSA', 'INAPTA', 'BAIXADA')
        ->and($negatives)->not->toContain('ATIVA');
});

it('tells negative from active/empty', function () {
    expect(SituacaoCadastral::isNegative('BAIXADA'))->toBeTrue()
        ->and(SituacaoCadastral::isNegative('baixada'))->toBeTrue() // case-insensitive
        ->and(SituacaoCadastral::isNegative('ATIVA'))->toBeFalse()
        ->and(SituacaoCadastral::isNegative(''))->toBeFalse()
        ->and(SituacaoCadastral::isNegative(null))->toBeFalse();
});
