<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\RefreshMonitoredCompanyJob;
use App\Models\MonitoredCompany;
use Illuminate\Console\Command;

/**
 * Re-coleta TODA a carteira (todas as organizações), enfileirando um refresh por
 * empresa. Pensado para rodar após cada dump mensal da base CNPJ (V2-F3): como a
 * fonte local não tem rate limit, processa tudo em lote — o snapshot/diff/alerta
 * roda por empresa e gera alertas do que mudou entre um dump e outro.
 *
 * `--rebaseline` grava os snapshots SEM gerar alertas: use uma única vez ao
 * trocar a fonte de dados (BrasilAPI → base local), para a 1ª coleta local não
 * disparar alertas de mera diferença de formato.
 */
final class RecollectPortfolios extends Command
{
    protected $signature = 'vigilo:recoletar-carteira
        {--rebaseline : Grava snapshots sem gerar alertas (use 1x ao trocar a fonte de dados)}';

    protected $description = 'Re-coleta toda a carteira (após o dump mensal). --rebaseline não gera alertas.';

    public function handle(): int
    {
        $rebaseline = (bool) $this->option('rebaseline');
        $count = 0;

        MonitoredCompany::query()
            ->select(['id', 'portfolio_id', 'cnpj', 'label'])
            ->chunkById(300, function ($chunk) use ($rebaseline, &$count): void {
                foreach ($chunk as $company) {
                    RefreshMonitoredCompanyJob::dispatch($company, $rebaseline);
                    $count++;
                }
            });

        $verbo = $rebaseline ? 'Re-baseline (sem alertas)' : 'Re-coleta';
        $this->info("{$verbo} enfileirada para {$count} empresa(s).");

        if ($count > 0) {
            $this->line('Dica: com a fonte local, defina CNPJ_THROTTLE_PER_MINUTE=0 para processar sem throttle.');
        }

        return self::SUCCESS;
    }
}
