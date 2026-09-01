<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RefreshStatus;
use App\Support\Cnpj;
use Database\Factories\MonitoredCompanyFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $portfolio_id
 * @property string $cnpj
 * @property string|null $label
 * @property Carbon|null $last_refreshed_at
 * @property RefreshStatus|null $last_refresh_status
 * @property string|null $last_refresh_error
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Portfolio $portfolio
 * @property-read Collection<int, CompanySnapshot> $snapshots
 * @property-read CompanySnapshot|null $latestSnapshot
 * @property-read Collection<int, ChangeEvent> $changeEvents
 */
class MonitoredCompany extends Model
{
    /** @use HasFactory<MonitoredCompanyFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['portfolio_id', 'cnpj', 'label', 'last_refreshed_at', 'last_refresh_status', 'last_refresh_error'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_refreshed_at' => 'datetime',
            'last_refresh_status' => RefreshStatus::class,
        ];
    }

    public function formattedCnpj(): string
    {
        return Cnpj::tryFrom($this->cnpj)?->formatted() ?? $this->cnpj;
    }

    /**
     * @return BelongsTo<Portfolio, $this>
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    /**
     * @return HasMany<CompanySnapshot, $this>
     */
    public function snapshots(): HasMany
    {
        return $this->hasMany(CompanySnapshot::class);
    }

    /**
     * Most recent snapshot captured for this company.
     *
     * @return HasOne<CompanySnapshot, $this>
     */
    public function latestSnapshot(): HasOne
    {
        return $this->hasOne(CompanySnapshot::class)->latestOfMany('captured_at');
    }

    /**
     * @return HasMany<ChangeEvent, $this>
     */
    public function changeEvents(): HasMany
    {
        return $this->hasMany(ChangeEvent::class);
    }
}
