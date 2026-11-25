<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Identificador opaco de uma pessoa PROVÁVEL (nome + CPF mascarado) — vai na
 * URL da página da pessoa no lugar do nome/CPF. Mesma conta do
 * deploy/cnpj-db/bin/pessoas-popular.sh: `md5(documento || '|' || nome)::uuid`,
 * com o nome aparado só de espaços (como o `btrim` do Postgres) e documento
 * vazio para estrangeiro sem documento.
 */
final class PessoaChave
{
    public static function para(?string $documento, string $nome): string
    {
        $h = md5(($documento ?? '').'|'.trim($nome, ' '));

        return substr($h, 0, 8).'-'.substr($h, 8, 4).'-'.substr($h, 12, 4).'-'.substr($h, 16, 4).'-'.substr($h, 20);
    }

    public static function valida(string $chave): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $chave) === 1;
    }
}
