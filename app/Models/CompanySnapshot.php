<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CompanySnapshotFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $monitored_company_id
 * @property Carbon $captured_at
 * @property string $razao_social
 * @property string|null $nome_fantasia
 * @property string $situacao_cadastral
 * @property Carbon|null $situacao_data
 * @property string|null $cnae_principal
 * @property string|null $porte
 * @property string|null $natureza_juridica
 * @property string|null $logradouro
 * @property string|null $municipio
 * @property string|null $uf
 * @property array<string, mixed> $raw_json
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MonitoredCompany $monitoredCompany
 * @property-read Collection<int, CompanyPartner> $partners
 */
class CompanySnapshot extends Model
{
    /** @use HasFactory<CompanySnapshotFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'monitored_company_id',
        'captured_at',
        'razao_social',
        'nome_fantasia',
        'situacao_cadastral',
        'situacao_data',
        'cnae_principal',
        'porte',
        'natureza_juridica',
        'logradouro',
        'municipio',
        'uf',
        'raw_json',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
            'situacao_data' => 'date',
            'raw_json' => 'array',
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
     * @return HasMany<CompanyPartner, $this>
     */
    public function partners(): HasMany
    {
        return $this->hasMany(CompanyPartner::class);
    }
}
