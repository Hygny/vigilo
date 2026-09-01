<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PortfolioScheduledRunFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Registro de uma execução agendada mensal de um portfólio.
 *
 * @property int $id
 * @property int $portfolio_id
 * @property int $scheduled_day
 * @property Carbon $ran_on
 * @property int $companies_count
 * @property int $dispatched_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Portfolio $portfolio
 */
class PortfolioScheduledRun extends Model
{
    /** @use HasFactory<PortfolioScheduledRunFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['portfolio_id', 'scheduled_day', 'ran_on', 'companies_count', 'dispatched_count'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ran_on' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Portfolio, $this>
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
