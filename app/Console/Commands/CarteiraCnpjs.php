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
 *
 * Com --pending, imprime só os ainda não conferidos contra a PGFN
 * (pgfn_synced_at nulo) — o refrescar-pgfn-novos.sh usa isso para detectar
 * empresa nova e carregar a dívida dela do cache sem esperar o trimestre.
 */
final class CarteiraCnpjs extends Command
{
    protected $signature = 'vigilo:carteira-cnpjs {--pending : Só os ainda não sincronizados com a PGFN}';

    protected $description = 'Imprime os CNPJs (14 dígitos) monitorados, um por linha (para pré-filtrar dumps).';

    public function handle(): int
    {
        $query = MonitoredCompany::query();

        if ($this->option('pending')) {
            $query->whereNull('pgfn_synced_at');
        }

        $query
            ->pluck('cnpj')
            ->map(fn ($cnpj): string => preg_replace('/\D/', '', (string) $cnpj) ?? '')
            ->filter(fn (string $digits): bool => strlen($digits) === 14)
            ->unique()
            ->sort() // ordena pelos dígitos normalizados (não pela forma crua armazenada)
            ->each(fn (string $digits) => $this->line($digits));

        return self::SUCCESS;
    }
}
