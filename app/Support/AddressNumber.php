<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Normaliza um número de logradouro para comparação: caixa alta, sem o prefixo
 * "Nº"/"N°" e sem espaços, de modo que "Nº 320" e "320" casem. Fonte única
 * usada pela busca por endereço (API OSINT) e pelo grafo (vizinhos de endereço),
 * tanto em PHP ({@see normalize()}) quanto no SQL ({@see sqlExpression()}) — o
 * filtro de número roda NO BANCO para ser exaustivo (sem amostragem) e rápido.
 */
final class AddressNumber
{
    /** Prefixos "número" removidos na normalização (já em caixa alta). */
    private const TOKENS = ['Nº', 'N°', 'N.º', 'N.°', 'Nº.', 'N º', 'N °'];

    public static function normalize(string $value): string
    {
        $upper = str_replace(self::TOKENS, '', mb_strtoupper(trim($value)));

        return preg_replace('/\s+/', '', $upper) ?? $upper;
    }

    /**
     * Expressão SQL que normaliza uma coluna de número DO MESMO jeito que
     * {@see normalize()} (upper + tira "Nº"/variantes + tira espaços), para o
     * filtro rodar NO BANCO. Só `upper()`/`replace()` — portável entre PostgreSQL
     * (produção) e SQLite (testes). Retorna um LITERAL por coluna (as únicas são
     * `e.numero` e `ea.numero`): garante injection-safety e satisfaz o
     * analisador, que exige literal-string em SQL cru. A paridade com
     * {@see normalize()} é coberta pelos testes do por-endereço ("Nº 320" ≡ "320").
     *
     * @return literal-string
     */
    public static function sqlExpression(string $column): string
    {
        return match ($column) {
            'e.numero' => "replace(replace(replace(replace(replace(replace(replace(replace(upper(coalesce(e.numero, '')), 'Nº', ''), 'N°', ''), 'N.º', ''), 'N.°', ''), 'Nº.', ''), 'N º', ''), 'N °', ''), ' ', '')",
            'ea.numero' => "replace(replace(replace(replace(replace(replace(replace(replace(upper(coalesce(ea.numero, '')), 'Nº', ''), 'N°', ''), 'N.º', ''), 'N.°', ''), 'Nº.', ''), 'N º', ''), 'N °', ''), ' ', '')",
            default => throw new \InvalidArgumentException("Coluna de número não suportada em SQL: {$column}"),
        };
    }
}
