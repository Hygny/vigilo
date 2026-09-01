<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Outcome of a CSV import: which CNPJs were created and which rows were rejected
 * (with the reason and the originating line, for a legible report).
 */
final readonly class ImportReport
{
    /**
     * @param  list<string>  $imported  Normalized CNPJs that were created.
     * @param  list<array{line: int, value: string, reason: string}>  $rejected
     */
    public function __construct(
        public array $imported = [],
        public array $rejected = [],
    ) {}

    public function importedCount(): int
    {
        return count($this->imported);
    }

    public function rejectedCount(): int
    {
        return count($this->rejected);
    }

    /**
     * @return list<array{line: int, value: string, reason: string}>
     */
    public function rejectedWithReason(string $reason): array
    {
        return array_values(array_filter(
            $this->rejected,
            fn (array $row): bool => $row['reason'] === $reason,
        ));
    }
}
