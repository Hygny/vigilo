<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Normaliza um número de logradouro para comparação: caixa alta, sem o prefixo
 * "Nº"/"N°" e sem espaços, de modo que "Nº 320" e "320" casem. Fonte única
 * usada pela busca por endereço (API OSINT) e pelo grafo (vizinhos de endereço).
 */
final class AddressNumber
{
    public static function normalize(string $value): string
    {
        $upper = mb_strtoupper(trim($value));
        $upper = str_replace(['Nº', 'N°', 'N.º', 'N.°', 'Nº.', 'N º', 'N °'], '', $upper);

        return preg_replace('/\s+/', '', $upper) ?? $upper;
    }
}
