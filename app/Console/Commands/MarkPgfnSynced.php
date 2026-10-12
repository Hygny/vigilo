<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MonitoredCompany;
use Illuminate\Console\Command;

/**
 * Marca TODOS os CNPJs monitorados como conferidos contra a PGFN agora
 * (pgfn_synced_at = now). Chamado pelo baixar-importar-pgfn.sh ao final de um
 * import bem-sucedido: como o import filtra a carteira inteira, todos passaram —
 * tendo dívida ou não. Assim `--pending` fica vazio até entrar empresa nova.
 */
final class MarkPgfnSynced extends Command
{
    protected $signature = 'vigilo:pgfn-marcar-sincronizado';

    protected $description = 'Marca todos os CNPJs monitorados como sincronizados com a PGFN (pgfn_synced_at = agora).';

    public function handle(): int
    {
        $n = MonitoredCompany::query()->update(['pgfn_synced_at' => now()]);

        $this->info("PGFN: {$n} empresa(s) marcada(s) como sincronizada(s).");

        return self::SUCCESS;
    }
}
