<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Carbon;

/**
 * Uma consulta a dado de pessoa física (busca por nome ou página da pessoa) —
 * trilha de auditoria (LGPD). Append-only, podada pela retenção configurada.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $organization_id
 * @property string $tipo
 * @property string $termo
 * @property Carbon $created_at
 */
class PessoaConsulta extends Model
{
    use Prunable;

    public const TIPO_BUSCA = 'busca';

    public const TIPO_PERFIL = 'perfil';

    public const UPDATED_AT = null;

    protected $table = 'pessoa_consultas';

    /** @var list<string> */
    protected $fillable = ['user_id', 'organization_id', 'tipo', 'termo'];

    public static function registrar(User $user, string $tipo, string $termo): void
    {
        self::query()->create([
            'user_id' => $user->getKey(),
            'organization_id' => $user->organization_id,
            'tipo' => $tipo,
            'termo' => mb_substr($termo, 0, 160),
        ]);
    }

    /** @return Builder<self> */
    public function prunable(): Builder
    {
        return self::query()->where('created_at', '<', now()->subDays(max(1, (int) config('cnpj.pessoas.retencao_dias', 365))));
    }
}
