<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CompanySanctionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Uma sanção conhecida de uma empresa monitorada (CGU: CEIS/CNEP/CEPIM). O
 * conjunto por empresa é a base do diff que detecta sanção NOVA.
 *
 * @property int $id
 * @property int $monitored_company_id
 * @property string $fonte
 * @property string $fingerprint
 * @property string|null $tipo
 * @property string|null $orgao
 * @property string|null $data_inicio
 * @property string|null $data_fim
 * @property Carbon $detected_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MonitoredCompany $monitoredCompany
 */
class CompanySanction extends Model
{
    /** @use HasFactory<CompanySanctionFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'monitored_company_id',
        'fonte',
        'fingerprint',
        'tipo',
        'orgao',
        'data_inicio',
        'data_fim',
        'detected_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'detected_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<MonitoredCompany, $this>
     */
    public function monitoredCompany(): BelongsTo
    {
        return $this->belongsTo(MonitoredCompany::class);
    }
}
