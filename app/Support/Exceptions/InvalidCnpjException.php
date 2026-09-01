<?php

declare(strict_types=1);

namespace App\Support\Exceptions;

use InvalidArgumentException;

final class InvalidCnpjException extends InvalidArgumentException
{
    public static function for(string $value): self
    {
        return new self("O CNPJ informado é inválido: [{$value}].");
    }
}
