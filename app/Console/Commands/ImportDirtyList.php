<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DirtyListEmployer;
use App\Support\CsvColumns;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Importa a "Lista Suja" do trabalho escravo (Cadastro de Empregadores — MTE) de
 * um CSV para a tabela `dirty_list_employers`. A lista é republicada inteira a
 * cada atualização, então a importação é RECARGA TOTAL (trunca e regrava num
 * único átomo).
 *
 * O CSV é mapeado por NOME de coluna (fuzzy), tolerando variações do cabeçalho
 * (ex.: "CNPJ/CPF", "Empregador", "UF", "Ano"). A única coluna obrigatória é a do
 * documento (CNPJ/CPF). Baixe o arquivo dos dados abertos do MTE/SmartLab.
 */
final class ImportDirtyList extends Command
{
    protected $signature = 'vigilo:importar-lista-suja
        {path : Caminho do CSV da Lista Suja}
        {--delimiter=; : Delimitador do CSV (padrão ";")}';

    protected $description = 'Importa a Lista Suja do trabalho escravo (MTE) de um CSV (recarga total).';

    public function handle(): int
    {
        $path = (string) $this->argument('path');

        if (! is_file($path)) {
            $this->error("Arquivo não encontrado: {$path}");

            return self::FAILURE;
        }

        $delimiter = (string) $this->option('delimiter');
        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->error("Não foi possível abrir: {$path}");

            return self::FAILURE;
        }

        try {
            $header = fgetcsv($handle, 0, $delimiter);

            if (! is_array($header)) {
                $this->error('CSV vazio ou ilegível.');

                return self::FAILURE;
            }

            $col = [
                'documento' => CsvColumns::find($header, ['cnpj', 'cpf', 'documento']),
                'nome' => CsvColumns::find($header, ['empregador', 'razao', 'nome']),
                'uf' => CsvColumns::find($header, ['uf', 'estado']),
                'ano' => CsvColumns::find($header, ['ano']),
                'estabelecimento' => CsvColumns::find($header, ['estabelecimento']),
            ];

            if ($col['documento'] === null) {
                $this->error('Não achei a coluna de CNPJ/CPF no cabeçalho do CSV.');

                return self::FAILURE;
            }

            $now = Carbon::now();
            $rows = [];

            while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
                $documento = preg_replace('/\D/', '', CsvColumns::cell($line, $col['documento']) ?? '') ?? '';

                // Só CNPJ (14) ou CPF (11). Descarta lixo (cabeçalho repetido,
                // linha vazia e, sobretudo, delimitador/coluna errados — que
                // concatenam dígitos de várias colunas e dariam um "documento"
                // implausível). É também a guarda que evita apagar a lista boa.
                if (! in_array(strlen($documento), [11, 14], true)) {
                    continue;
                }

                $rows[] = [
                    'documento' => $documento,
                    'nome' => CsvColumns::cell($line, $col['nome']),
                    'uf' => CsvColumns::cell($line, $col['uf']),
                    'ano' => CsvColumns::cell($line, $col['ano']),
                    'estabelecimento' => CsvColumns::cell($line, $col['estabelecimento']),
                    'imported_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        } finally {
            fclose($handle);
        }

        // Guarda contra wipe acidental: se nada válido foi lido (arquivo vazio,
        // cabeçalho não reconhecido ou --delimiter errado), NÃO toca na tabela —
        // a lista atual é um sinal de risco crítico e não pode sumir em silêncio.
        if ($rows === []) {
            $this->error('Nenhum CNPJ/CPF válido encontrado — arquivo vazio, cabeçalho não reconhecido ou --delimiter errado? A lista atual NÃO foi alterada.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($rows): void {
            DirtyListEmployer::query()->delete(); // recarga total: a lista é republicada inteira

            foreach (array_chunk($rows, 1000) as $chunk) {
                DirtyListEmployer::insert($chunk);
            }
        });

        $this->info('Lista Suja importada: '.count($rows).' registros.');

        return self::SUCCESS;
    }
}
