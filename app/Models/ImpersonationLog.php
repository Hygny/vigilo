<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Registro de auditoria de uma personificação (super-admin entrando como um
 * usuário). E-mails são gravados denormalizados para o log sobreviver à
 * exclusão dos usuários.
 *
 * @property int $id
 * @property int $impersonator_id
 * @property string $impersonator_email
 * @property int $impersonated_user_id
 * @property string $impersonated_email
 * @property int|null $organization_id
 * @property Carbon|null $created_at
 */
class ImpersonationLog extends Model
{
    public const UPDATED_AT = null;

    /** @var list<string> */
    protected $fillable = [
        'impersonator_id',
        'impersonator_email',
        'impersonated_user_id',
        'impersonated_email',
        'organization_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
