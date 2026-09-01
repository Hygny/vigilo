<?php

declare(strict_types=1);

namespace App\Notifications;

use App\DTO\ImportReport;
use App\Models\Portfolio;
use Illuminate\Notifications\Notification;

/**
 * In-app report delivered after an asynchronous CSV import finishes.
 */
class CompaniesImported extends Notification
{
    public function __construct(
        public Portfolio $portfolio,
        public ImportReport $report,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'companies_imported',
            'portfolio_id' => $this->portfolio->id,
            'portfolio_name' => $this->portfolio->name,
            'imported' => $this->report->importedCount(),
            'rejected' => $this->report->rejectedCount(),
            'rejected_rows' => $this->report->rejected,
        ];
    }
}
