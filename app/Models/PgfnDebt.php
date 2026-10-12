<?php

declare(strict_types=1);

namespace App\Models;

use App\Console\Commands\ImportPgfn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Uma inscrição em dívida ativa da União (PGFN) de uma empresa da carteira.
 * `documento` são os dígitos do CNPJ. Tabela de referência recarregada (por tipo)
 * por {@see ImportPgfn}.
 *
 * @property int $id
 * @property string $documento
 * @property string $tipo
 * @property string|null $situacao
 * @property string|null $receita
 * @property string|null $valor
 * @property string|null $data_inscricao
 * @property Carbon $imported_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PgfnDebt extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'documento',
        'tipo',
        'situacao',
        'receita',
        'valor',
        'data_inscricao',
        'imported_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'imported_at' => 'datetime',
        ];
    }
}
