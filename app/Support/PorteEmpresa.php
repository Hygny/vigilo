<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Código de porte da Receita (base CNPJ) → rótulo. Fonte única — usada pelo
 * LocalCnpjProvider (snapshot da carteira) e pelo histórico cadastral.
 * 00 (não informado) e códigos desconhecidos viram null.
 */
final class PorteEmpresa
{
    /** @var array<string, string> */
    private const MAP = [
        '01' => 'ME',
        '03' => 'EPP',
        '05' => 'DEMAIS',
    ];

    public static function label(mixed $code): ?string
    {
        return self::MAP[trim(is_scalar($code) ? (string) $code : '')] ?? null;
    }
}
