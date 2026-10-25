<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Normalização da busca por nome — espelho em PHP da `busca_norm()` do Postgres
 * (deploy/cnpj-db/sql/busca.sql): sem acento, maiúsculas, só letras/dígitos
 * separados por espaço, espaço nas pontas, CPF de 11 dígitos no fim removido.
 * A chave com espaço nas pontas é o que deixa `LIKE '% TERMO%'` casar começo de
 * palavra. Os dois lados têm de bater: os testes de cada um usam os mesmos
 * exemplos (tests/deploy/cnpj-db-sql.test.sh e BuscaNomeTest).
 */
final class BuscaNome
{
    /** Termos usados no máximo (além disso a busca não fica mais precisa, só mais cara). */
    public const MAX_TERMOS = 6;

    /** Pelo menos um termo com este tamanho — senão o índice não filtra nada. */
    public const MIN_TERMO_FORTE = 3;

    public static function chave(?string $texto): string
    {
        $t = strtoupper(Str::ascii((string) $texto));
        // Só um número de EXATAMENTE 11 dígitos no fim (não-dígito antes): "… 12345678901"
        // é CPF de MEI; "EMPRESA 123456789012" fica como está.
        $t = preg_replace('/(^|[^0-9])[0-9]{11}\s*$/', '$1', $t) ?? '';
        $t = preg_replace('/[^A-Z0-9]+/', ' ', $t) ?? '';

        return ' '.trim($t).' ';
    }

    /**
     * Termos de busca a partir do que o usuário digitou: palavras normalizadas
     * de 2+ caracteres, sem repetição, sem número com cara de CPF (busca por CPF
     * não existe). Vazio quando nenhum termo tem {@see MIN_TERMO_FORTE}+
     * caracteres.
     *
     * @return list<string>
     */
    public static function termos(string $consulta): array
    {
        $palavras = preg_split('/\s+/', trim(self::chave($consulta))) ?: [];

        $termos = array_values(array_unique(array_filter(
            $palavras,
            fn (string $p): bool => strlen($p) >= 2 && preg_match('/^\d{11}$/', $p) !== 1,
        )));

        $forte = array_filter($termos, fn (string $t): bool => strlen($t) >= self::MIN_TERMO_FORTE);

        return $forte === [] ? [] : array_slice($termos, 0, self::MAX_TERMOS);
    }

    /** O texto digitado parece um CPF (11 dígitos)? Busca por CPF é recusada. */
    public static function pareceCpf(string $consulta): bool
    {
        return preg_match('/^\d{11}$/', preg_replace('/\D/', '', $consulta) ?? '') === 1
            && preg_match('/[A-Za-z]/', $consulta) !== 1;
    }
}
