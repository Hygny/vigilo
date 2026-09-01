<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CompanyPartnerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $company_snapshot_id
 * @property string|null $documento
 * @property string $nome
 * @property string|null $qualificacao
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CompanySnapshot $companySnapshot
 */
class CompanyPartner extends Model
{
    /** @use HasFactory<CompanyPartnerFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['company_snapshot_id', 'documento', 'nome', 'qualificacao'];

    /**
     * @return BelongsTo<CompanySnapshot, $this>
     */
    public function companySnapshot(): BelongsTo
    {
        return $this->belongsTo(CompanySnapshot::class);
    }
}
