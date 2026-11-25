<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Carbon;
use Throwable;

/**
 * Data vinda da base da Receita (coluna date ou texto) → AAAA-MM-DD. Vazia,
 * zerada ("0", "0000-00-00") ou ilegível → null. Fonte única para os leitores
 * novos da base (grafo, detalhes do perfil).
 */
final class DataReceita
{
    public static function ymd(mixed $value): ?string
    {
        $raw = is_scalar($value) ? trim((string) $value) : '';

        if ($raw === '' || $raw === '0' || str_starts_with($raw, '0000')) {
            return null;
        }

        try {
            return Carbon::parse($raw)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }
}
