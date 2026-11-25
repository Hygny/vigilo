<?php

declare(strict_types=1);

namespace App\Services\Risco\Exceptions;

use RuntimeException;

/**
 * A API de Dados do Portal da Transparência não respondeu (conexão/erro HTTP)
 * ou não está configurada. Quem consome (painel de risco, contratos) captura e
 * degrada. `$alvo` = o que se consultava (CEIS, CNEP, CEPIM, contratos...).
 */
final class PortalUnavailable extends RuntimeException
{
    public static function notConfigured(): self
    {
        return new self('Integração com o Portal da Transparência não configurada.');
    }

    public static function unreachable(string $alvo, string $detail): self
    {
        return new self("Portal da Transparência inacessível ao consultar {$alvo}: {$detail}");
    }

    public static function requestFailed(string $alvo, int $status): self
    {
        return new self("Portal da Transparência retornou HTTP {$status} ao consultar {$alvo}.");
    }
}
