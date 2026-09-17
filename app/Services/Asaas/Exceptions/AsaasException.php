<?php

declare(strict_types=1);

namespace App\Services\Asaas\Exceptions;

use RuntimeException;

/**
 * Falha ao falar com a API do Asaas (rede, credencial ou resposta de erro).
 */
final class AsaasException extends RuntimeException
{
    public static function notConfigured(): self
    {
        return new self('Integração Asaas não configurada: defina ASAAS_API_KEY no .env.');
    }

    public static function requestFailed(string $endpoint, int $status, string $body): self
    {
        $body = mb_substr($body, 0, 500);

        return new self("Asaas respondeu {$status} em [{$endpoint}]: {$body}");
    }

    public static function unreachable(string $endpoint, string $reason): self
    {
        return new self("Asaas inacessível em [{$endpoint}]: {$reason}");
    }
}
