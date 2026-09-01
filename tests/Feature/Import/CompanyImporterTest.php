<?php

declare(strict_types=1);

use App\Models\Portfolio;
use App\Services\CompanyImporter;

it('imports valid rows and rejects empties, invalids and duplicates', function () {
    $portfolio = Portfolio::factory()->create();
    $portfolio->monitoredCompanies()->create(['cnpj' => '11222333000181', 'label' => 'existente']);

    $rows = [
        ['cnpj' => '11.444.777/0001-61', 'label' => 'Cliente A'], // valid -> imported
        ['cnpj' => '11.222.333/0001-81'],                         // duplicate of existing
        ['cnpj' => '123'],                                        // invalid check digits
        ['cnpj' => ''],                                           // empty
        ['cnpj' => '11444777000161'],                             // duplicate within the file
    ];

    $report = (new CompanyImporter)->import($portfolio, $rows);

    expect($report->importedCount())->toBe(1)
        ->and($report->imported)->toBe(['11444777000161'])
        ->and($report->rejectedCount())->toBe(4)
        ->and($report->rejectedWithReason(CompanyImporter::REASON_DUPLICATE))->toHaveCount(2)
        ->and($report->rejectedWithReason(CompanyImporter::REASON_INVALID))->toHaveCount(1)
        ->and($report->rejectedWithReason(CompanyImporter::REASON_EMPTY))->toHaveCount(1);

    $this->assertDatabaseHas('monitored_companies', [
        'portfolio_id' => $portfolio->id,
        'cnpj' => '11444777000161',
        'label' => 'Cliente A',
    ]);

    expect($portfolio->monitoredCompanies()->count())->toBe(2);
});

it('normalizes CNPJs with dropped leading zeros before storing', function () {
    $portfolio = Portfolio::factory()->create();

    // 01234567800043 is a valid CNPJ; a spreadsheet would drop its leading zero,
    // leaving 1234567800043 (13 digits), which normalization must recover.
    $report = (new CompanyImporter)->import($portfolio, [
        ['cnpj' => '1234567800043'],
    ]);

    expect($report->importedCount())->toBe(1)
        ->and($report->imported[0])->toBe('01234567800043');
});

it('parses a CSV file with a header and reports the offending line', function () {
    $portfolio = Portfolio::factory()->create();

    $path = tempnam(sys_get_temp_dir(), 'vigilo_csv');
    file_put_contents($path, "cnpj,label\n11.444.777/0001-61,Cliente A\n123,Ruim\n");

    $report = (new CompanyImporter)->importFromFile($portfolio, $path);
    @unlink($path);

    expect($report->importedCount())->toBe(1)
        ->and($report->rejectedWithReason(CompanyImporter::REASON_INVALID))->toHaveCount(1)
        ->and($report->rejected[0]['line'])->toBe(3); // header=1, valid=2, invalid=3
});

it('supports semicolon-delimited files', function () {
    $portfolio = Portfolio::factory()->create();

    $path = tempnam(sys_get_temp_dir(), 'vigilo_csv');
    file_put_contents($path, "cnpj;label\n11.444.777/0001-61;Cliente A\n");

    $report = (new CompanyImporter)->importFromFile($portfolio, $path);
    @unlink($path);

    expect($report->importedCount())->toBe(1)
        ->and($report->imported[0])->toBe('11444777000161');
});

it('numbers array rows from 1 when no explicit line is given', function () {
    $portfolio = Portfolio::factory()->create();

    $report = (new CompanyImporter)->import($portfolio, [
        ['cnpj' => '123'], // inválido, primeira linha do array
    ]);

    expect($report->rejected[0]['line'])->toBe(1);
});
