<?php

declare(strict_types=1);

namespace App\Services\Risco\Exceptions;

use App\Enums\FonteSancao;
use RuntimeException;

/**
 * A API de Dados do Portal da Transparência não respondeu (conexão/erro HTTP)
 * ou não está configurada. O RiscoService captura e degrada o painel de risco.
 */
final class PortalUnavailable extends RuntimeException
{
    public static function notConfigured(): self
    {
        return new self('Integração de risco (Portal da Transparência) não configurada.');
    }

    public static function unreachable(FonteSancao $fonte, string $detail): self
    {
        return new self("Portal da Transparência inacessível ao consultar {$fonte->sigla()}: {$detail}");
    }

    public static function requestFailed(FonteSancao $fonte, int $status): self
    {
        return new self("Portal da Transparência retornou HTTP {$status} ao consultar {$fonte->sigla()}.");
    }
}
