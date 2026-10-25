<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Razão social para EXIBIÇÃO. A Receita registra o MEI como "NOME DA PESSOA
 * 12345678901" — o CPF inteiro no fim do nome. Por LGPD, o Vigilo nunca mostra
 * CPF íntegro: os 11 dígitos finais saem mascarados no mesmo formato da base
 * (`***NNNNNN**`).
 */
final class RazaoSocial
{
    public static function exibir(?string $razao): ?string
    {
        if ($razao === null) {
            return null;
        }

        return preg_replace_callback(
            '/(?<!\d)(\d{3})(\d{6})(\d{2})\s*$/',
            fn (array $m): string => '***'.$m[2].'**',
            trim($razao),
        ) ?? $razao;
    }
}
