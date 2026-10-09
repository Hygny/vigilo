<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Registro de uma tentativa de entrega do webhook de saída (resultado final).
 * Append-only (só `created_at`).
 *
 * @property int $id
 * @property int $organization_id
 * @property string $event_id
 * @property string $event_type
 * @property string|null $cnpj
 * @property int $changes_count
 * @property string $status
 * @property int|null $response_status
 * @property string|null $error
 * @property Carbon|null $created_at
 */
final class WebhookDelivery extends Model
{
    public const UPDATED_AT = null;

    /** @var list<string> */
    protected $fillable = [
        'organization_id',
        'event_id',
        'event_type',
        'cnpj',
        'changes_count',
        'status',
        'response_status',
        'error',
    ];

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
