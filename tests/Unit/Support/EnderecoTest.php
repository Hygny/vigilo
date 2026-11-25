<?php

declare(strict_types=1);

use App\Support\Endereco;

it('matches the same address and rejects what does not identify one', function (array $a, array $b, bool $esperado) {
    expect(Endereco::mesmoLocal(...$a, ...$b))->toBe($esperado);
})->with([
    'igual' => [['01001-000', '10', null], ['01001000', '10', null], true],
    'Nº e zeros na sala' => [['01001000', 'Nº 10', 'SALA 01'], ['01001000', '10', 'sala 1'], true],
    'um lado sem sala' => [['01001000', '10', 'SALA 1'], ['01001000', '10', ''], true],
    'salas diferentes' => [['01001000', '10', 'SALA 1'], ['01001000', '10', 'SALA 2'], false],
    'outro número' => [['01001000', '10', null], ['01001000', '12', null], false],
    'sem número' => [['01001000', 'S/N', null], ['01001000', 'S/N', null], false],
    'CEP especial' => [['01001900', '10', null], ['01001900', '10', null], false],
    'CEP zerado' => [['00000000', '10', null], ['00000000', '10', null], false],
]);
