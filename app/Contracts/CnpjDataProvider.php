<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTO\CompanyData;
use App\Providers\Cnpj\Exceptions\CnpjProviderException;
use App\Support\Exceptions\InvalidCnpjException;

interface CnpjDataProvider
{
    /**
     * Fetch normalized cadastral data for a CNPJ.
     *
     * Returns null when the company is not found (HTTP 404). Throws when the
     * input is not a valid CNPJ, or when the upstream service fails after the
     * configured retries (timeout, rate limit, 5xx).
     *
     * @throws InvalidCnpjException
     * @throws CnpjProviderException
     */
    public function fetch(string $cnpj): ?CompanyData;
}
