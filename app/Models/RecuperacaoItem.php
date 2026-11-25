<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Bens\DevedorMonitor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Item conhecido de uma devedora monitorada (bem, crédito ou possível
 * sucessora) — base de comparação do {@see DevedorMonitor}.
 *
 * @property int $id
 * @property int $monitored_company_id
 * @property string $tipo aeronave | participacao | contrato | sucessora
 * @property string $chave marca | raiz do CNPJ | id do contrato
 * @property Carbon $visto_em
 * @property int $ausencias rodadas seguidas sem o item
 * @property-read MonitoredCompany $monitoredCompany
 */
class RecuperacaoItem extends Model
{
    protected $table = 'recuperacao_itens';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = ['monitored_company_id', 'tipo', 'chave', 'visto_em', 'ausencias'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['visto_em' => 'datetime'];
    }

    /** @return BelongsTo<MonitoredCompany, $this> */
    public function monitoredCompany(): BelongsTo
    {
        return $this->belongsTo(MonitoredCompany::class);
    }
}
