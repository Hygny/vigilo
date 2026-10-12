<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MonitoredCompany;
use Illuminate\Console\Command;

/**
 * Imprime os CNPJs (14 dígitos) de TODAS as carteiras monitoradas, um por linha,
 * sem pontuação. Usado pelo deploy/baixar-importar-pgfn.sh para pré-filtrar o
 * dump gigante da PGFN no host (grep) antes de importar — o importador ainda
 * refiltra pela carteira, então este comando só precisa reduzir volume.
 */
final class CarteiraCnpjs extends Command
{
    protected $signature = 'vigilo:carteira-cnpjs';

    protected $description = 'Imprime os CNPJs (14 dígitos) monitorados, um por linha (para pré-filtrar dumps).';

    public function handle(): int
    {
        MonitoredCompany::query()
            ->pluck('cnpj')
            ->map(fn ($cnpj): string => preg_replace('/\D/', '', (string) $cnpj) ?? '')
            ->filter(fn (string $digits): bool => strlen($digits) === 14)
            ->unique()
            ->sort() // ordena pelos dígitos normalizados (não pela forma crua armazenada)
            ->each(fn (string $digits) => $this->line($digits));

        return self::SUCCESS;
    }
}
