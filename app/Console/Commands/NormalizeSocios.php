<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\NomeSocio;
use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Popula `socios.nome_norm` (nome normalizado para a busca por sócio da API
 * OSINT) e cria o índice. Roda na base CNPJ (PostgreSQL).
 *
 * Por padrão é **incremental e retomável**: só processa quem está pendente
 * (`nome_norm IS NULL`). Na 1ª carga isso é a tabela inteira (tudo NULL); nas
 * rodadas mensais, só os sócios NOVOS que o reimport (upsert) inseriu — rápido.
 * Um cancelamento não custa recomeçar: o que já foi normalizado não é NULL, a
 * re-execução continua de onde parou (o índice em `nome_norm` torna o scan de
 * pendentes barato).
 *
 * `--all` reprocessa TODOS os sócios — use só para uma varredura completa (ex.:
 * se nomes foram corrigidos na origem, caso raro; o reimport não mexe no
 * `nome_norm` de uma linha existente, então uma correção de nome ficaria
 * defasada até um `--all`).
 *
 * A normalização usa a MESMA função {@see NomeSocio::norm()} que a API aplica na
 * entrada — é o que garante o match.
 */
final class NormalizeSocios extends Command
{
    protected $signature = 'vigilo:normalizar-socios
        {--chunk=5000 : Linhas por lote}
        {--all : Reprocessa TODOS os sócios, não só os pendentes (varredura completa)}';

    protected $description = 'Popula socios.nome_norm (+ índice) para a busca por sócio da API OSINT.';

    public function handle(): int
    {
        $connection = (string) config('cnpj.providers.local.connection', 'cnpj');
        $db = DB::connection($connection);
        $chunk = max(500, (int) $this->option('chunk'));

        if (! Schema::connection($connection)->hasColumn('socios', 'nome_norm')) {
            $this->info("Adicionando a coluna nome_norm em socios (conexão {$connection})...");
            Schema::connection($connection)->table('socios', function (Blueprint $table): void {
                $table->text('nome_norm')->nullable();
            });
        }

        // Índice cedo: o scan de pendentes (nome_norm IS NULL) o aproveita.
        $db->statement('CREATE INDEX IF NOT EXISTS idx_socios_nome_norm ON socios (nome_norm)');

        $processed = 0;

        if ($this->option('all')) {
            $this->info('Reprocessando TODOS os sócios (--all)...');

            $db->table('socios')->select('socio_id', 'nome_socio')->orderBy('socio_id')->chunkById($chunk, function (Collection $rows) use ($db, &$processed): void {
                $this->applyChunk($db, $rows);
                $processed += $rows->count();
                $this->line("  normalizados: {$processed}");
            }, 'socio_id');
        } else {
            $this->info('Normalizando pendentes (nome_norm IS NULL)...');

            while (true) {
                $rows = $db->table('socios')
                    ->whereNull('nome_norm')
                    ->limit($chunk)
                    ->get(['socio_id', 'nome_socio']);

                if ($rows->isEmpty()) {
                    break;
                }

                $this->applyChunk($db, $rows);
                $processed += $rows->count();
                $this->line("  normalizados: {$processed}");
            }
        }

        $this->info("Pronto. {$processed} sócios normalizados.");

        return self::SUCCESS;
    }

    /** Linhas por UPDATE em lote — teto defensivo de bindings (SQLite ~32k, PG 65k). */
    private const UPDATE_BATCH = 1000;

    /**
     * Grava o `nome_norm` do lote num único UPDATE por sub-lote (CASE), em vez de
     * um UPDATE por linha — reduz drasticamente os round-trips na carga inicial
     * (DT-14). A normalização continua PHP-side ({@see NomeSocio::norm}).
     *
     * @param  Collection<int, \stdClass>  $rows
     */
    private function applyChunk(ConnectionInterface $db, Collection $rows): void
    {
        foreach ($rows->chunk(self::UPDATE_BATCH) as $batch) {
            $this->updateBatch($db, $batch);
        }
    }

    /**
     * @param  Collection<int, \stdClass>  $batch
     */
    private function updateBatch(ConnectionInterface $db, Collection $batch): void
    {
        $cases = [];
        $ids = [];
        $caseBindings = [];

        foreach ($batch as $row) {
            $s = (array) $row;
            $id = $s['socio_id'] ?? null;

            if ($id === null) {
                continue;
            }

            $cases[] = 'WHEN ? THEN ?';
            $caseBindings[] = $id;
            $caseBindings[] = NomeSocio::norm((string) ($s['nome_socio'] ?? ''));
            $ids[] = $id;
        }

        if ($ids === []) {
            return;
        }

        $in = implode(', ', array_fill(0, count($ids), '?'));
        $sql = 'UPDATE socios SET nome_norm = CASE socio_id '.implode(' ', $cases).' END WHERE socio_id IN ('.$in.')';

        $db->update($sql, array_merge($caseBindings, $ids));
    }
}
