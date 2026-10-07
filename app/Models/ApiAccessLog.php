<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Uma linha da trilha de auditoria da API OSINT de vigilância. Append-only:
 * só tem `created_at` (sem `updated_at`).
 *
 * @property int $id
 * @property int|null $token_id
 * @property int|null $user_id
 * @property string $method
 * @property string $path
 * @property array<string, mixed>|null $params
 * @property int|null $status
 * @property string|null $ip
 */
final class ApiAccessLog extends Model
{
    public const UPDATED_AT = null;

    /** @var list<string> */
    protected $fillable = [
        'token_id',
        'user_id',
        'method',
        'path',
        'params',
        'status',
        'ip',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'params' => 'array',
        ];
    }
}
