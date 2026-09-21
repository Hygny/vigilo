<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Traduz o código de situação cadastral da Receita (base CNPJ) para o texto que
 * o resto do sistema usa. Fonte única — consumido pelo LocalCnpjProvider e pelo
 * grafo societário. Código desconhecido/vazio → '' (o baseline do CompanyDiffer
 * trata '' como não-negativo).
 */
final class SituacaoCadastral
{
    /** @var array<string, string> */
    private const MAP = [
        '01' => 'NULA',
        '02' => 'ATIVA',
        '03' => 'SUSPENSA',
        '04' => 'INAPTA',
        '08' => 'BAIXADA',
    ];

    public static function label(mixed $code): string
    {
        return self::MAP[trim((string) ($code ?? ''))] ?? '';
    }
}
