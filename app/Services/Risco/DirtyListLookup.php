<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\Models\DirtyListEmployer;

/**
 * Consulta a Lista Suja do trabalho escravo (tabela local, indexada por
 * documento). Leitura barata — não precisa de cache nem de HTTP.
 */
final class DirtyListLookup
{
    /** A empresa (CNPJ) consta na Lista Suja? Devolve a entrada, ou null. */
    public function consta(string $cnpj): ?DirtyListEmployer
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== 14) {
            return null;
        }

        return DirtyListEmployer::query()->where('documento', $digits)->first();
    }
}
