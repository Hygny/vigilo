<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Services\CompanyDiffer;

/**
 * A single detected change between two snapshots.
 *
 * Produced by {@see CompanyDiffer} (a pure function) and later
 * persisted as a change_events row by the refresh job. It intentionally carries
 * no timestamps or foreign keys — those belong to the persistence layer, not to
 * the diffing logic.
 */
final readonly class ChangeEventData
{
    public function __construct(
        public ChangeType $type,
        public string $field,
        public ?string $oldValue,
        public ?string $newValue,
        public Severity $severity,
    ) {}
}
