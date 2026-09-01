<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ImportReport;
use App\Models\Portfolio;
use App\Support\Cnpj;
use Generator;
use Illuminate\Database\UniqueConstraintViolationException;

/**
 * Imports monitored companies into a portfolio from CSV rows.
 *
 * Each row is normalized and validated (mod-11), de-duplicated against both the
 * file itself and the companies already in the portfolio, and either created or
 * rejected with a reason. Nothing here does authorization — the caller must have
 * verified the user owns the portfolio.
 */
final class CompanyImporter
{
    public const REASON_EMPTY = 'cnpj_vazio';

    public const REASON_INVALID = 'cnpj_invalido';

    public const REASON_DUPLICATE = 'duplicado';

    /**
     * @param  iterable<int, array{cnpj?: string|null, label?: string|null, line?: int}>  $rows
     */
    public function import(Portfolio $portfolio, iterable $rows): ImportReport
    {
        /** @var array<string, true> $existing */
        $existing = array_fill_keys(
            $portfolio->monitoredCompanies()->pluck('cnpj')->all(),
            true,
        );

        /** @var array<string, true> $seen */
        $seen = [];

        $imported = [];
        $rejected = [];
        $counter = 0;

        foreach ($rows as $row) {
            $counter++;
            $line = $row['line'] ?? $counter;
            $rawCnpj = trim((string) ($row['cnpj'] ?? ''));

            if ($rawCnpj === '') {
                $rejected[] = ['line' => $line, 'value' => '', 'reason' => self::REASON_EMPTY];

                continue;
            }

            $cnpj = Cnpj::tryFrom($rawCnpj);

            if ($cnpj === null) {
                $rejected[] = ['line' => $line, 'value' => $rawCnpj, 'reason' => self::REASON_INVALID];

                continue;
            }

            $normalized = $cnpj->value;

            if (isset($seen[$normalized]) || isset($existing[$normalized])) {
                $rejected[] = ['line' => $line, 'value' => $normalized, 'reason' => self::REASON_DUPLICATE];

                continue;
            }

            $seen[$normalized] = true;
            $label = trim((string) ($row['label'] ?? ''));

            try {
                $portfolio->monitoredCompanies()->create([
                    'cnpj' => $normalized,
                    'label' => $label !== '' ? $label : null,
                ]);
                $imported[] = $normalized;
            } catch (UniqueConstraintViolationException) {
                // Corrida: outro import inseriu o mesmo CNPJ neste portfólio.
                $rejected[] = ['line' => $line, 'value' => $normalized, 'reason' => self::REASON_DUPLICATE];
            }
        }

        return new ImportReport($imported, $rejected);
    }

    public function importFromFile(Portfolio $portfolio, string $path): ImportReport
    {
        return $this->import($portfolio, $this->readCsv($path));
    }

    /**
     * Stream rows from a CSV file. Accepts a `cnpj[,label]` header (comma or
     * semicolon delimited); without a recognizable header the first two columns
     * are taken as cnpj and label positionally.
     *
     * @return Generator<int, array{cnpj: string, label: ?string, line: int}>
     */
    private function readCsv(string $path): Generator
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return;
        }

        $delimiter = $this->sniffDelimiter($path);
        $cnpjIndex = 0;
        $labelIndex = 1;
        $headerParsed = false;
        $line = 0;

        try {
            while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
                $line++;

                if ($row === [null]) {
                    continue; // blank line
                }

                $cells = array_map(fn ($cell): string => trim((string) ($cell ?? '')), $row);

                if (! $headerParsed) {
                    $headerParsed = true;
                    $lower = array_map(fn (string $cell): string => mb_strtolower($cell), $cells);
                    $cnpjHeader = array_search('cnpj', $lower, true);

                    if ($cnpjHeader !== false) {
                        $cnpjIndex = (int) $cnpjHeader;
                        $labelHeader = array_search('label', $lower, true);
                        $labelIndex = $labelHeader === false ? -1 : (int) $labelHeader;

                        continue; // consume the header row
                    }
                }

                $label = ($labelIndex >= 0 && isset($cells[$labelIndex])) ? $cells[$labelIndex] : null;

                yield [
                    'cnpj' => $cells[$cnpjIndex] ?? '',
                    'label' => ($label === null || $label === '') ? null : $label,
                    'line' => $line,
                ];
            }
        } finally {
            fclose($handle);
        }
    }

    private function sniffDelimiter(string $path): string
    {
        // Lê só a primeira linha — não carrega o arquivo inteiro na memória.
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return ',';
        }

        $firstLine = fgets($handle);
        fclose($handle);
        $firstLine = $firstLine === false ? '' : $firstLine;

        return substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
    }
}
