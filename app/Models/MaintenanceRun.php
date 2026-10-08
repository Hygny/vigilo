<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Registro de uma rodada de manutenção mensal da base CNPJ, gravado pelo
 * `vigilo:maintenance-record` (chamado pelo reimport-mensal.sh). Lido pelo
 * super-admin em /admin.
 *
 * @property int $id
 * @property string $kind
 * @property string $status
 * @property string|null $reimport_status
 * @property string|null $recoleta_status
 * @property string|null $normalizar_status
 * @property string|null $message
 * @property Carbon|null $started_at
 * @property Carbon|null $finished_at
 */
final class MaintenanceRun extends Model
{
    /** Rotina de manutenção mensal da base CNPJ (reimport + recoleta + normalizar). */
    public const KIND_CNPJ = 'cnpj_mensal';

    /** @var list<string> */
    protected $fillable = [
        'kind',
        'status',
        'reimport_status',
        'recoleta_status',
        'normalizar_status',
        'message',
        'started_at',
        'finished_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Rodadas da manutenção mensal da base CNPJ, mais recentes primeiro.
     *
     * @param  Builder<MaintenanceRun>  $query
     */
    public function scopeCnpjMensal(Builder $query): void
    {
        $query->where('kind', self::KIND_CNPJ)
            ->orderByDesc('finished_at')
            ->orderByDesc('id');
    }
}
