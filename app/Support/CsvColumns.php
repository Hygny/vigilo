<?php

declare(strict_types=1);

namespace App\Support;

use App\Console\Commands\ImportDirtyList;
use App\Console\Commands\ImportPgfn;
use Illuminate\Support\Str;

/**
 * Mapeamento tolerante de colunas de CSV por NOME (fuzzy) — usado pelos
 * importadores de dados abertos (Lista Suja, PGFN) para casar cabeçalhos que
 * variam de layout. Fonte única: {@see ImportDirtyList},
 * {@see ImportPgfn}.
 */
final class CsvColumns
{
    /**
     * Índice da coluna que casa um dos termos (sem acento/caixa). Prefere um
     * cabeçalho IGUAL ao termo antes de um que apenas o CONTÉM — assim colunas
     * como `SITUACAO_INSCRICAO` ganham de `TIPO_SITUACAO_INSCRICAO`, que também
     * contém "situacao". Sem match exato, cai no 1º cabeçalho que contém o termo.
     *
     * @param  array<int, string|null>  $header
     * @param  list<string>  $terms
     */
    public static function find(array $header, array $terms): ?int
    {
        // 1ª preferência: cabeçalho exatamente igual a um dos termos.
        foreach ($header as $i => $nome) {
            if (in_array(Str::lower(Str::ascii((string) $nome)), $terms, true)) {
                return $i;
            }
        }

        // Fallback: 1ª coluna cujo cabeçalho contém um dos termos.
        foreach ($header as $i => $nome) {
            $normalizado = Str::lower(Str::ascii((string) $nome));

            foreach ($terms as $termo) {
                if (str_contains($normalizado, $termo)) {
                    return $i;
                }
            }
        }

        return null;
    }

    /**
     * Valor (trim) de uma coluna da linha, ou null se ausente/vazio.
     *
     * @param  array<int, string|null>  $line
     */
    public static function cell(array $line, ?int $index): ?string
    {
        if ($index === null) {
            return null;
        }

        $value = trim((string) ($line[$index] ?? ''));

        return $value === '' ? null : $value;
    }
}
