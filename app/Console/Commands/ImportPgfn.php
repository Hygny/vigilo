<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MonitoredCompany;
use App\Models\PgfnDebt;
use App\Support\CsvColumns;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Importa a Dívida Ativa da União (PGFN, dados abertos) de um CSV, guardando SÓ
 * as inscrições dos CNPJs da CARTEIRA (o dump oficial tem ~6M linhas; filtrar
 * pela carteira deixa a tabela pequena). RECARGA POR TIPO: o `--tipo` (arquivo
 * previdenciário/não-previdenciário/FGTS) é substituído a cada import, então
 * rodar os 3 arquivos coexiste. CPF (PF, mascarado) é ignorado — só CNPJ.
 *
 * Dados abertos: https://dadosabertos.pgfn.gov.br (Dívida Ativa da União).
 */
final class ImportPgfn extends Command
{
    protected $signature = 'vigilo:importar-pgfn
        {path : Caminho do CSV da PGFN}
        {--tipo=geral : Categoria recarregada (previdenciario|nao-previdenciario|fgts|geral)}
        {--delimiter=; : Delimitador do CSV (padrão ";")}';

    protected $description = 'Importa a dívida ativa (PGFN) dos CNPJs da carteira de um CSV (recarga por tipo).';

    /** Linhas por INSERT em lote (a tabela é pequena — só a carteira). */
    private const CHUNK = 1000;

    public function handle(): int
    {
        $path = (string) $this->argument('path');

        if (! is_file($path)) {
            $this->error("Arquivo não encontrado: {$path}");

            return self::FAILURE;
        }

        $tipo = (string) $this->option('tipo');
        $delimiter = (string) $this->option('delimiter');

        // Conjunto de CNPJs monitorados (só dígitos) — o filtro da carteira.
        $carteira = [];
        foreach (MonitoredCompany::query()->pluck('cnpj') as $cnpj) {
            $digits = preg_replace('/\D/', '', (string) $cnpj) ?? '';
            if (strlen($digits) === 14) {
                $carteira[$digits] = true;
            }
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->error("Não foi possível abrir: {$path}");

            return self::FAILURE;
        }

        $now = Carbon::now();
        $rows = [];
        $sawValidDoc = false;

        try {
            $header = fgetcsv($handle, 0, $delimiter);

            if (! is_array($header)) {
                $this->error('CSV vazio ou ilegível.');

                return self::FAILURE;
            }

            $col = [
                'documento' => CsvColumns::find($header, ['cnpj', 'cpf', 'documento']),
                'situacao' => CsvColumns::find($header, ['situacao_inscricao', 'situacao']),
                'receita' => CsvColumns::find($header, ['receita']),
                'valor' => CsvColumns::find($header, ['valor']),
                'data' => CsvColumns::find($header, ['data_inscricao', 'data']),
            ];

            if ($col['documento'] === null) {
                $this->error('Não achei a coluna de CNPJ/CPF no cabeçalho do CSV.');

                return self::FAILURE;
            }

            while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
                $documento = preg_replace('/\D/', '', CsvColumns::cell($line, $col['documento']) ?? '') ?? '';

                if (in_array(strlen($documento), [11, 14], true)) {
                    $sawValidDoc = true; // o arquivo tem documentos plausíveis
                }

                // Só CNPJ (14) da carteira — PF (mascarado) e não-monitorados fora.
                if (strlen($documento) !== 14 || ! isset($carteira[$documento])) {
                    continue;
                }

                $rows[] = [
                    'documento' => $documento,
                    'tipo' => $tipo,
                    'situacao' => CsvColumns::cell($line, $col['situacao']),
                    'receita' => CsvColumns::cell($line, $col['receita']),
                    'valor' => $this->parseValor(CsvColumns::cell($line, $col['valor'])),
                    'data_inscricao' => $this->parseData(CsvColumns::cell($line, $col['data'])),
                    'imported_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        } finally {
            fclose($handle);
        }

        // Guarda: se o arquivo tinha linhas mas NENHUM documento válido (cabeçalho
        // não reconhecido ou --delimiter errado), não mexe na tabela.
        if (! $sawValidDoc) {
            $this->error('Nenhum CNPJ/CPF válido no arquivo — cabeçalho não reconhecido ou --delimiter errado? Nada foi alterado.');

            return self::FAILURE;
        }

        // Recarga por tipo: substitui as inscrições deste tipo (0 linhas = limpa).
        DB::transaction(function () use ($rows, $tipo): void {
            PgfnDebt::query()->where('tipo', $tipo)->delete();

            foreach (array_chunk($rows, self::CHUNK) as $chunk) {
                PgfnDebt::insert($chunk);
            }
        });

        $this->info("PGFN [{$tipo}]: {$this->plural(count($rows))} da carteira importada(s).");

        return self::SUCCESS;
    }

    private function plural(int $n): string
    {
        return $n.' '.($n === 1 ? 'inscrição' : 'inscrições');
    }

    /**
     * "1.234,56" → "1234.56" (formato BR); null se vazio/não-numérico. Se não há
     * vírgula, o ponto (quando houver) é tratado como decimal — evita que um
     * valor já em ponto decimal ("1234.56") vire "123456" (erro de 100×) caso o
     * layout do dump mude.
     */
    private function parseValor(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $v = str_contains($raw, ',')
            ? str_replace(',', '.', str_replace(['.', ' '], '', $raw)) // BR: ponto = milhar
            : str_replace(' ', '', $raw);                              // ponto já é decimal

        return is_numeric($v) ? $v : null;
    }

    /** dd/mm/aaaa ou aaaa-mm-dd → Y-m-d; null se não parsear. */
    private function parseData(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        foreach (['d/m/Y', 'Y-m-d'] as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $raw);

            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }
}
