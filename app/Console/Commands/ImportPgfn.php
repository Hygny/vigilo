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
 * Importa a Dívida Ativa da União (PGFN, dados abertos) de um CSV — ou de uma
 * PASTA de CSVs — guardando SÓ as inscrições dos CNPJs da CARTEIRA (o dump
 * oficial tem milhões de linhas; filtrar pela carteira deixa a tabela pequena).
 *
 * O dump real vem num ZIP por sistema, com um CSV POR UF dentro (e SP quebrado
 * em partes). Por isso o comando aceita uma pasta: lê todos os .csv e faz UM
 * único reload por `--tipo` cobrindo todas as UFs (apontar um arquivo por vez
 * apagaria a UF anterior). RECARGA POR TIPO: o `--tipo` (previdenciário /
 * não-previdenciário / FGTS) é substituído a cada import, então rodar os 3
 * coexiste. CPF (PF, mascarado) é ignorado — só CNPJ.
 *
 * Dados abertos: https://dadosabertos.pgfn.gov.br (Dívida Ativa da União).
 * O script deploy/baixar-importar-pgfn.sh baixa, descompacta e chama este comando.
 */
final class ImportPgfn extends Command
{
    protected $signature = 'vigilo:importar-pgfn
        {path : Caminho de um CSV OU de uma pasta com os CSVs da PGFN (por UF)}
        {--tipo=geral : Categoria recarregada (previdenciario|nao-previdenciario|fgts|geral)}
        {--delimiter=; : Delimitador do CSV (padrão ";")}
        {--allow-empty : Permite recarregar com ZERO inscrição (limpa o tipo) — para arquivos já validados, p.ex. pré-filtrados pelo pgfn_filter.py}';

    protected $description = 'Importa a dívida ativa (PGFN) dos CNPJs da carteira de um CSV ou pasta (recarga por tipo).';

    /** Linhas por INSERT em lote (a tabela é pequena — só a carteira). */
    private const CHUNK = 1000;

    public function handle(): int
    {
        $files = $this->resolveFiles((string) $this->argument('path'));

        if ($files === null) {
            return self::FAILURE; // mensagem já reportada
        }

        $tipo = (string) $this->option('tipo');
        $delimiter = (string) $this->option('delimiter');
        $carteira = $this->carteira();
        $now = Carbon::now();

        /** @var list<array<string, mixed>> $rows */
        $rows = [];
        $sawValidDoc = false;

        // Lê TODOS os arquivos antes de tocar na tabela — se algum falhar, aborta
        // sem ter apagado nada (a recarga só acontece na transação lá embaixo).
        foreach ($files as $file) {
            $result = $this->readFile($file, $delimiter, $carteira, $tipo, $now);

            if ($result === null) {
                return self::FAILURE;
            }

            $sawValidDoc = $sawValidDoc || $result['sawValidDoc'];

            foreach ($result['rows'] as $row) {
                $rows[] = $row;
            }
        }

        // Guarda: se os arquivos tinham linhas mas NENHUM documento válido
        // (cabeçalho não reconhecido ou --delimiter errado), não mexe na tabela —
        // a menos que --allow-empty diga que o arquivo já foi validado e que zero
        // inscrição é um resultado legítimo (limpar o tipo).
        if (! $sawValidDoc && ! $this->option('allow-empty')) {
            $this->error('Nenhum CNPJ/CPF válido lido — cabeçalho não reconhecido ou --delimiter errado? Nada foi alterado.');

            return self::FAILURE;
        }

        // Recarga por tipo: substitui as inscrições deste tipo (0 linhas = limpa).
        DB::transaction(function () use ($rows, $tipo): void {
            PgfnDebt::query()->where('tipo', $tipo)->delete();

            foreach (array_chunk($rows, self::CHUNK) as $chunk) {
                PgfnDebt::insert($chunk);
            }
        });

        $this->info("PGFN [{$tipo}]: {$this->plural(count($rows))} da carteira importada(s) de ".count($files).' arquivo(s).');

        return self::SUCCESS;
    }

    /**
     * Resolve o argumento para a lista de CSVs a ler: um arquivo, ou todos os
     * .csv de uma pasta (ordenados). Null (com erro reportado) se não existir.
     *
     * @return list<string>|null
     */
    private function resolveFiles(string $path): ?array
    {
        if (is_dir($path)) {
            $found = glob(rtrim($path, '/\\').'/*.[cC][sS][vV]') ?: [];
            sort($found);

            if ($found === []) {
                $this->error("Nenhum .csv encontrado na pasta: {$path}");

                return null;
            }

            return $found;
        }

        if (is_file($path)) {
            return [$path];
        }

        $this->error("Arquivo ou pasta não encontrado: {$path}");

        return null;
    }

    /**
     * Conjunto de CNPJs monitorados (só dígitos) — o filtro da carteira.
     *
     * @return array<string, true>
     */
    private function carteira(): array
    {
        $carteira = [];

        foreach (MonitoredCompany::query()->pluck('cnpj') as $cnpj) {
            $digits = preg_replace('/\D/', '', (string) $cnpj) ?? '';

            if (strlen($digits) === 14) {
                $carteira[$digits] = true;
            }
        }

        return $carteira;
    }

    /**
     * Lê um CSV, devolvendo as linhas da carteira + se viu algum documento
     * plausível. Null (erro reportado) se ilegível ou sem a coluna de documento.
     *
     * @param  array<string, true>  $carteira
     * @return array{rows: list<array<string, mixed>>, sawValidDoc: bool}|null
     */
    private function readFile(string $file, string $delimiter, array $carteira, string $tipo, Carbon $now): ?array
    {
        $handle = fopen($file, 'r');

        if ($handle === false) {
            $this->error("Não foi possível abrir: {$file}");

            return null;
        }

        /** @var list<array<string, mixed>> $rows */
        $rows = [];
        $sawValidDoc = false;

        try {
            $header = fgetcsv($handle, 0, $delimiter);

            if (! is_array($header)) {
                $this->error("CSV vazio ou ilegível: {$file}");

                return null;
            }

            $col = [
                'documento' => CsvColumns::find($header, ['cnpj', 'cpf', 'documento']),
                'situacao' => CsvColumns::find($header, ['situacao_inscricao', 'situacao']),
                'receita' => CsvColumns::find($header, ['receita']),
                'valor' => CsvColumns::find($header, ['valor']),
                'data' => CsvColumns::find($header, ['data_inscricao', 'data']),
            ];

            if ($col['documento'] === null) {
                $this->error("Não achei a coluna de CNPJ/CPF no cabeçalho: {$file}");

                return null;
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
                    'situacao' => $this->toUtf8(CsvColumns::cell($line, $col['situacao'])),
                    'receita' => $this->toUtf8(CsvColumns::cell($line, $col['receita'])),
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

        return ['rows' => $rows, 'sawValidDoc' => $sawValidDoc];
    }

    private function plural(int $n): string
    {
        return $n.' '.($n === 1 ? 'inscrição' : 'inscrições');
    }

    /**
     * Garante texto em UTF-8. O dump da PGFN é Latin-1 (ISO-8859-1); bytes
     * acentuados crus ("dívida", "Contribuições") são UTF-8 inválido e o MySQL
     * (utf8mb4) rejeita (erro 1366). Se já for UTF-8 válido, mantém; senão,
     * converte de Latin-1 — cobre tanto o CSV do pgfn_filter.py quanto o import
     * manual de um CSV cru da PGFN.
     */
    private function toUtf8(?string $value): ?string
    {
        if ($value === null || mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
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
