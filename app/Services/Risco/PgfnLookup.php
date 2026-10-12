<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Risco\PgfnDebtSummary;
use App\Models\PgfnDebt;

/**
 * Consulta a dívida ativa (PGFN) de um CNPJ na tabela local (filtrada pela
 * carteira na importação). Leitura barata e indexada — sem HTTP, sem cache.
 */
final class PgfnLookup
{
    /** Resumo da dívida do CNPJ, ou null se não há inscrição. */
    public function para(string $cnpj): ?PgfnDebtSummary
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== 14) {
            return null;
        }

        $valores = PgfnDebt::query()->where('documento', $digits)->pluck('valor');

        if ($valores->isEmpty()) {
            return null;
        }

        $total = 0.0;
        foreach ($valores as $valor) {
            $total += (float) ($valor ?? 0);
        }

        return new PgfnDebtSummary($valores->count(), $total);
    }
}
