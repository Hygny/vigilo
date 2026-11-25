<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Sinais de risco conhecidos de UMA empresa ligada (pela raiz do CNPJ) a uma
 * empresa da carteira — base de comparação do monitor do "risco ao redor".
 *
 * @property int $id
 * @property int $monitored_company_id
 * @property string $cnpj_basico
 * @property string $sinais códigos ordenados, separados por vírgula
 * @property Carbon $visto_em
 * @property int $ausencias rodadas seguidas sem a ligada no grafo
 * @property-read MonitoredCompany $monitoredCompany
 */
class CompanyNeighborFlag extends Model
{
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['monitored_company_id', 'cnpj_basico', 'sinais', 'visto_em', 'ausencias'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['visto_em' => 'datetime'];
    }

    /** @return list<string> */
    public function codigos(): array
    {
        return $this->sinais === '' ? [] : explode(',', $this->sinais);
    }

    /** @return BelongsTo<MonitoredCompany, $this> */
    public function monitoredCompany(): BelongsTo
    {
        return $this->belongsTo(MonitoredCompany::class);
    }
}
