<?php

declare(strict_types=1);

namespace App\Models;

use App\Console\Commands\ImportDirtyList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Uma entrada da Lista Suja do trabalho escravo (MTE). `documento` são os dígitos
 * do CNPJ/CPF do empregador. Sem factory/relações: é uma tabela de referência
 * recarregada por {@see ImportDirtyList}.
 *
 * @property int $id
 * @property string $documento
 * @property string|null $nome
 * @property string|null $uf
 * @property string|null $ano
 * @property string|null $estabelecimento
 * @property Carbon $imported_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DirtyListEmployer extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'documento',
        'nome',
        'uf',
        'ano',
        'estabelecimento',
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
