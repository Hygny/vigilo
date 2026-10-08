<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\NomeSocio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Popula `socios.nome_norm` (nome normalizado para a busca por sócio da API
 * OSINT) e cria o índice. Roda na base CNPJ (PostgreSQL): 1x após a carga e de
 * novo após cada reimport mensal (o pipeline recria a tabela). É PESADO na
 * primeira vez (28M linhas) — rode em horário de baixo uso.
 *
 * A normalização usa a MESMA função {@see NomeSocio::norm()} que a API aplica na
 * entrada — é o que garante o match.
 */
final class NormalizeSocios extends Command
{
    protected $signature = 'vigilo:normalizar-socios {--chunk=5000 : Linhas por lote}';

    protected $description = 'Popula socios.nome_norm (+ índice) para a busca por sócio da API OSINT.';

    public function handle(): int
    {
        $connection = (string) config('cnpj.providers.local.connection', 'cnpj');
        $db = DB::connection($connection);
        $chunk = max(500, (int) $this->option('chunk'));

        $this->info("Garantindo a coluna nome_norm em socios (conexão {$connection})...");
        $db->statement('ALTER TABLE socios ADD COLUMN IF NOT EXISTS nome_norm text');

        $this->info('Normalizando nomes (pode demorar na primeira vez)...');
        $processed = 0;

        $db->table('socios')
            ->orderBy('socio_id')
            ->chunkById($chunk, function ($rows) use ($db, &$processed): void {
                foreach ($rows as $row) {
                    $db->table('socios')
                        ->where('socio_id', $row->socio_id)
                        ->update(['nome_norm' => NomeSocio::norm((string) ($row->nome_socio ?? ''))]);
                }

                $processed += $rows->count();
                $this->line("  normalizados: {$processed}");
            }, 'socio_id');

        $this->info('Criando índice idx_socios_nome_norm...');
        $db->statement('CREATE INDEX IF NOT EXISTS idx_socios_nome_norm ON socios (nome_norm)');

        $this->info("Pronto. {$processed} sócios normalizados.");

        return self::SUCCESS;
    }
}
