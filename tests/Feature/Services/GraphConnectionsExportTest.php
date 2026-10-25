<?php

declare(strict_types=1);

use App\DTO\Graph\GraphConnection;
use App\DTO\Risco\PgfnDebtSummary;
use App\Services\Export\GraphConnectionsExport;
use PhpOffice\PhpSpreadsheet\IOFactory;

/** @return list<list<mixed>> linhas da planilha gerada (sem o cabeçalho) */
function linhasDoExport(bool $pgfnDisponivel): array
{
    $path = (new GraphConnectionsExport)->build([
        new GraphConnection('EMPRESA GRUPO', '44.555.666/0001-99', 'Grupo econômico', 'ATIVA', new PgfnDebtSummary(2, 1250.5), consultaPgfn: true),
        new GraphConnection('EMPRESA LIMPA', '77.888.999/0001-55', 'Grupo econômico', 'ATIVA', null, consultaPgfn: true),
        new GraphConnection('MARIA', '***111**', 'Sócio', null),
    ], $pgfnDisponivel);

    $linhas = IOFactory::load($path)->getActiveSheet()->toArray();
    unlink($path);

    return array_slice($linhas, 1);
}

it('writes the PGFN debt column, blank only for "checked and no debt"', function () {
    $linhas = linhasDoExport(true);

    expect($linhas[0][4])->toBe('2 inscrições · R$ 1.250,50')
        ->and($linhas[1][4])->toBeNull()   // consultada, sem dívida
        ->and($linhas[2][4])->toBeNull();  // pessoa física: não se aplica
});

it('writes "não consultada" (never blank) for companies when the PGFN was unavailable', function () {
    $linhas = linhasDoExport(false);

    expect($linhas[0][4])->toBe('não consultada')
        ->and($linhas[1][4])->toBe('não consultada')
        ->and($linhas[2][4])->toBeNull();
});
