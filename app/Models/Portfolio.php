<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Database\Factories\PortfolioFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property list<int>|null $schedule_days
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Organization $organization
 * @property-read Collection<int, MonitoredCompany> $monitoredCompanies
 * @property-read int|null $monitored_companies_count
 * @property-read Collection<int, PortfolioScheduledRun> $scheduledRuns
 */
class Portfolio extends Model
{
    /** @use HasFactory<PortfolioFactory> */
    use BelongsToOrganization, HasFactory;

    /** @var list<string> */
    protected $fillable = ['organization_id', 'name', 'schedule_days'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'schedule_days' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return HasMany<MonitoredCompany, $this>
     */
    public function monitoredCompanies(): HasMany
    {
        return $this->hasMany(MonitoredCompany::class);
    }

    /**
     * @return HasMany<PortfolioScheduledRun, $this>
     */
    public function scheduledRuns(): HasMany
    {
        return $this->hasMany(PortfolioScheduledRun::class)->latest('ran_on');
    }
}
