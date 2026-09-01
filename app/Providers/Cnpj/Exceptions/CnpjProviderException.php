<?php

declare(strict_types=1);

namespace App\Providers\Cnpj\Exceptions;

use RuntimeException;

final class CnpjProviderException extends RuntimeException
{
    public static function requestFailed(string $cnpj, int $status): self
    {
        return new self("A consulta do CNPJ [{$cnpj}] falhou (HTTP {$status}).");
    }

    public static function unreachable(string $cnpj, string $reason): self
    {
        return new self("Não foi possível consultar o CNPJ [{$cnpj}]: {$reason}");
    }
}
