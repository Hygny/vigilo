<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Mostra a contagem de linhas das tabelas principais da base CNPJ própria
 * (conexão `cnpj`, PostgreSQL). Ferramenta de validação da carga do dump da
 * Receita (V2-F1): depois de importar, confere se `empresas`,
 * `estabelecimentos` e `socios` vieram populadas.
 */
final class CnpjBaseStatus extends Command
{
    protected $signature = 'cnpj:status';

    protected $description = 'Conta as linhas das tabelas da base CNPJ (conexão cnpj) para validar a carga.';

    /** @var list<string> */
    private const TABLES = ['empresas', 'estabelecimentos', 'socios'];

    public function handle(): int
    {
        try {
            $connection = DB::connection('cnpj');

            $rows = array_map(fn (string $table): array => [
                $table,
                number_format((int) $connection->table($table)->count(), 0, ',', '.'),
            ], self::TABLES);
        } catch (Throwable $e) {
            $this->error('Não foi possível consultar a base CNPJ (conexão cnpj): '.$e->getMessage());
            $this->line('Confira o serviço data-postgres-1 e as variáveis CNPJ_DB_* no .env.');

            return self::FAILURE;
        }

        $this->table(['Tabela', 'Linhas'], $rows);

        return self::SUCCESS;
    }
}
