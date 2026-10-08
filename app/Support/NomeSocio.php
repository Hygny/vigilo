<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Normaliza nome de sócio para comparação exata: sem acento, caixa alta e
 * espaços colapsados. Fonte ÚNICA da regra — usada tanto para normalizar a
 * entrada da API (`POST /empresas/por-socio`) quanto para popular a coluna
 * `socios.nome_norm` (comando `vigilo:normalizar-socios`). As duas pontas
 * passando pela mesma função garantem que o match não falhe por divergência.
 */
final class NomeSocio
{
    public static function norm(string $nome): string
    {
        $collapsed = preg_replace('/\s+/', ' ', trim($nome)) ?? '';

        return mb_strtoupper(Str::ascii($collapsed));
    }
}
