<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChangeType;
use App\Enums\Severity;
use Database\Factories\ChangeEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $monitored_company_id
 * @property int|null $from_snapshot_id
 * @property int $to_snapshot_id
 * @property ChangeType $type
 * @property string $field
 * @property string|null $old_value
 * @property string|null $new_value
 * @property Severity $severity
 * @property Carbon $detected_at
 * @property Carbon|null $acknowledged_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MonitoredCompany $monitoredCompany
 * @property-read CompanySnapshot|null $fromSnapshot
 * @property-read CompanySnapshot $toSnapshot
 */
class ChangeEvent extends Model
{
    /** @use HasFactory<ChangeEventFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'monitored_company_id',
        'from_snapshot_id',
        'to_snapshot_id',
        'type',
        'field',
        'old_value',
        'new_value',
        'severity',
        'detected_at',
        'acknowledged_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ChangeType::class,
            'severity' => Severity::class,
            'detected_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<MonitoredCompany, $this>
     */
    public function monitoredCompany(): BelongsTo
    {
        return $this->belongsTo(MonitoredCompany::class);
    }

    /**
     * @return BelongsTo<CompanySnapshot, $this>
     */
    public function fromSnapshot(): BelongsTo
    {
        return $this->belongsTo(CompanySnapshot::class, 'from_snapshot_id');
    }

    /**
     * @return BelongsTo<CompanySnapshot, $this>
     */
    public function toSnapshot(): BelongsTo
    {
        return $this->belongsTo(CompanySnapshot::class, 'to_snapshot_id');
    }

    public function isAcknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }
}
