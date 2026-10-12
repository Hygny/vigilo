<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MonitoredCompany;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Marca CNPJs monitorados como conferidos contra a PGFN agora (pgfn_synced_at =
 * now). Chamado pelo baixar-importar-pgfn.sh ao final de um import bem-sucedido.
 *
 * Com --from=<arquivo>, marca APENAS os CNPJs listados (o snapshot da carteira
 * que o import usou). Isso evita a corrida em que uma empresa adicionada DURANTE
 * o import — e que, por isso, NÃO teve a dívida carregada — seria marcada como
 * sincronizada e sumiria do --pending para sempre. Quem entra no meio não está
 * no snapshot → continua pendente → o próximo refresh pega. Sem --from, marca
 * todos (uso manual).
 */
final class MarkPgfnSynced extends Command
{
    protected $signature = 'vigilo:pgfn-marcar-sincronizado
        {--from= : Arquivo com os CNPJs conferidos (um por linha); sem ele, marca todos}';

    protected $description = 'Marca CNPJs monitorados como sincronizados com a PGFN (pgfn_synced_at = agora).';

    public function handle(): int
    {
        $from = $this->option('from');

        if ($from === null) {
            $n = $this->mark(MonitoredCompany::query());
            $this->info("PGFN: {$n} empresa(s) marcada(s) como sincronizada(s).");

            return self::SUCCESS;
        }

        if (! is_file($from)) {
            $this->error("Arquivo não encontrado: {$from}");

            return self::FAILURE;
        }

        $conferidos = $this->digitsFromFile($from);

        if ($conferidos === []) {
            $this->warn('Nenhum CNPJ válido no arquivo — nada marcado.');

            return self::SUCCESS;
        }

        // Normaliza o CNPJ de cada empresa (pode estar formatado no banco) e casa
        // com o conjunto do snapshot. Volume da carteira é pequeno → filtra em PHP.
        $ids = MonitoredCompany::query()
            ->get(['id', 'cnpj'])
            ->filter(fn (MonitoredCompany $c): bool => isset($conferidos[preg_replace('/\D/', '', (string) $c->cnpj) ?? '']))
            ->pluck('id')
            ->all();

        $n = $ids === [] ? 0 : $this->mark(MonitoredCompany::query()->whereIn('id', $ids));

        $this->info("PGFN: {$n} empresa(s) do snapshot marcada(s) como sincronizada(s).");

        return self::SUCCESS;
    }

    /**
     * Seta pgfn_synced_at=now preservando updated_at (a marcação não é uma
     * alteração "de negócio" da empresa).
     *
     * @param  Builder<MonitoredCompany>  $query
     */
    private function mark(Builder $query): int
    {
        return $query->update(['pgfn_synced_at' => now(), 'updated_at' => DB::raw('updated_at')]);
    }

    /**
     * Conjunto de CNPJs (14 dígitos) do arquivo, como chaves para lookup O(1).
     *
     * @return array<string, true>
     */
    private function digitsFromFile(string $path): array
    {
        $set = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $digits = preg_replace('/\D/', '', $line) ?? '';

            if (strlen($digits) === 14) {
                $set[$digits] = true;
            }
        }

        return $set;
    }
}
