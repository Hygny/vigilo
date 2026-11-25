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
 * @property string|null $pgfn_status
 * @property string|null $message
 * @property Carbon|null $started_at
 * @property Carbon|null $finished_at
 */
final class MaintenanceRun extends Model
{
    /** Rotina de manutenção mensal da base CNPJ (reimport + recoleta + normalizar). */
    public const KIND_CNPJ = 'cnpj_mensal';

    /** Backup diário do MySQL do app (deploy/backup-db.sh). */
    public const KIND_BACKUP = 'backup';

    /** Cópia local das sanções da CGU (vigilo:sancoes-cgu). */
    public const KIND_SANCOES_CGU = 'sancoes_cgu';

    /** Varredura diária de sanções da carteira (vigilo:varrer-sancoes). */
    public const KIND_VARREDURA = 'varredura_sancoes';

    /** Varredura diária do risco das empresas ligadas (vigilo:varrer-risco-ao-redor). */
    public const KIND_RISCO_AO_REDOR = 'risco_ao_redor';

    /** Cópia local do Registro Aeronáutico Brasileiro (vigilo:aeronaves-anac). */
    public const KIND_AERONAVES = 'aeronaves';

    /** Varredura diária das devedoras monitoradas (vigilo:varrer-devedores). */
    public const KIND_DEVEDORES = 'devedores';

    /** @var list<string> */
    protected $fillable = [
        'kind',
        'status',
        'reimport_status',
        'recoleta_status',
        'normalizar_status',
        'pgfn_status',
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
     * Registra uma rodada de rotina simples (sem passos): ok/falhou + mensagem.
     */
    public static function registrar(string $kind, bool $ok, ?string $message = null, ?Carbon $startedAt = null): self
    {
        $message = $message !== null ? mb_substr(trim($message), 0, 500) : null;

        return self::query()->create([
            'kind' => $kind,
            'status' => $ok ? 'success' : 'failed',
            'reimport_status' => 'skip',
            'recoleta_status' => 'skip',
            'normalizar_status' => 'skip',
            'message' => $message === '' ? null : $message,
            'started_at' => $startedAt ?? now(),
            'finished_at' => now(),
        ]);
    }

    /** A rodada mais recente de uma rotina (ou null se nunca rodou). */
    public static function ultima(string $kind): ?self
    {
        return self::query()->where('kind', $kind)->orderByDesc('finished_at')->orderByDesc('id')->first();
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
