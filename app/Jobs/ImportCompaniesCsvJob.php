<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Portfolio;
use App\Models\User;
use App\Notifications\CompaniesImported;
use App\Services\CompanyImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Imports a (potentially large) CSV of companies into a portfolio off the
 * request cycle, then notifies the requesting user with the resulting report
 * and removes the temporary upload.
 */
class ImportCompaniesCsvJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Portfolio $portfolio,
        public string $path,
        public ?int $notifyUserId = null,
    ) {}

    public function handle(CompanyImporter $importer): void
    {
        $report = $importer->importFromFile($this->portfolio, $this->path);

        if ($this->notifyUserId !== null) {
            User::find($this->notifyUserId)?->notify(new CompaniesImported($this->portfolio, $report));
        }

        if (is_file($this->path)) {
            @unlink($this->path);
        }
    }
}
