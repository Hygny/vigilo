<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\FonteSancao;
use App\Models\MaintenanceRun;
use App\Support\CsvColumns;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;
use ZipArchive;

/**
 * Baixa e carrega a cópia local dos cadastros de sanções da CGU (CEIS, CNEP,
 * CEPIM) a partir do "download de dados" do Portal da Transparência — um zip
 * por fonte, publicado em dias próprios de cada uma (só o mais recente fica no
 * ar). Procura o arquivo de hoje para trás e, se for um arquivo novo, RECARREGA
 * aquela fonte inteira num átomo. Só pessoa jurídica (CNPJ de 14 dígitos): o
 * cadastro traz CPF completo de pessoa física, que não guardamos.
 *
 * Nunca apaga a cópia boa por arquivo vazio/ilegível: sem nenhuma linha válida,
 * a fonte falha e fica como estava. Roda todo dia pelo agendador.
 */
final class ImportCguSancoes extends Command
{
    protected $signature = 'vigilo:sancoes-cgu
        {--fonte=* : ceis, cnep e/ou cepim (padrão: as três)}
        {--arquivo= : zip (ou csv) local em vez de baixar — exige uma única --fonte}
        {--data= : data do arquivo local (AAAA-MM-DD; padrão: a do nome do arquivo ou hoje)}
        {--forcar : recarrega mesmo que o arquivo mais recente já esteja carregado}';

    protected $description = 'Atualiza a cópia local das sanções da CGU (CEIS/CNEP/CEPIM) pelo download de dados do Portal da Transparência.';

    public function handle(): int
    {
        $fontes = $this->fontes();

        if ($fontes === null) {
            return self::FAILURE;
        }

        $arquivo = $this->option('arquivo');

        if (is_string($arquivo) && $arquivo !== '' && count($fontes) !== 1) {
            $this->error('--arquivo exige exatamente uma --fonte.');

            return self::FAILURE;
        }

        $falhas = 0;
        $inicio = now();
        $resumo = [];

        foreach ($fontes as $fonte) {
            try {
                $resumo[] = $fonte->sigla().': '.$this->carregar($fonte, is_string($arquivo) && $arquivo !== '' ? $arquivo : null);
            } catch (Throwable $e) {
                $falhas++;
                $resumo[] = $fonte->sigla().': FALHOU — '.$e->getMessage();
                $this->error("{$fonte->sigla()}: {$e->getMessage()}");
                Log::warning('vigilo:sancoes-cgu falhou', ['fonte' => $fonte->value, 'erro' => $e->getMessage()]);
            }
        }

        // Card "Rotinas" do /admin: só a rodada COMPLETA (as três fontes, baixadas)
        // — uma carga manual de uma fonte ou de um arquivo local não pode
        // mascarar a falha de outra fonte na rodada agendada.
        if ((! is_string($arquivo) || $arquivo === '') && array_filter((array) $this->option('fonte')) === []) {
            MaintenanceRun::registrar(MaintenanceRun::KIND_SANCOES_CGU, $falhas === 0, implode(' · ', $resumo), $inicio);
        }

        return $falhas === 0 ? self::SUCCESS : self::FAILURE;
    }

    /** @return list<FonteSancao>|null */
    private function fontes(): ?array
    {
        $pedidas = array_values(array_filter(array_map(
            fn (mixed $f): string => strtolower(trim(is_string($f) ? $f : '')),
            (array) $this->option('fonte'),
        )));

        if ($pedidas === []) {
            return FonteSancao::cases();
        }

        $fontes = [];

        foreach ($pedidas as $p) {
            $fonte = FonteSancao::tryFrom($p);

            if ($fonte === null) {
                $this->error("Fonte desconhecida: {$p} (use ceis, cnep ou cepim).");

                return null;
            }

            $fontes[$fonte->value] = $fonte;
        }

        return array_values($fontes);
    }

    /** Carrega uma fonte; devolve o resumo curto da rodada (para o card de Rotinas). */
    private function carregar(FonteSancao $fonte, ?string $arquivoLocal): string
    {
        if ($arquivoLocal !== null) {
            if (! is_file($arquivoLocal)) {
                throw new RuntimeException("arquivo não encontrado: {$arquivoLocal}");
            }

            $data = $this->dataDoArquivoLocal($arquivoLocal);

            return $this->gravar($fonte, $arquivoLocal, $data).' (arquivo de '.Carbon::parse($data)->format('d/m').')';
        }

        [$caminho, $data] = $this->baixarMaisRecente($fonte);
        $dia = Carbon::parse($data)->format('d/m');

        try {
            $atual = DB::table('cgu_cargas')->where('fonte', $fonte->value)->value('data_arquivo');

            if (! $this->option('forcar') && is_string($atual) && substr($atual, 0, 10) === $data) {
                $this->line("{$fonte->sigla()}: arquivo de ".Carbon::parse($data)->format('d/m/Y').' já carregado.');

                return "arquivo de {$dia} já carregado";
            }

            return $this->gravar($fonte, $caminho, $data)." (arquivo de {$dia})";
        } finally {
            @unlink($caminho);
        }
    }

    /**
     * Procura o zip da fonte de hoje para trás (cada fonte sai num dia; dias sem
     * arquivo respondem 403). Devolve [caminho temporário, data AAAA-MM-DD].
     *
     * @return array{0: string, 1: string}
     */
    private function baixarMaisRecente(FonteSancao $fonte): array
    {
        $base = rtrim((string) config('risco.cgu_download.base_url'), '/');
        $dias = max(1, (int) config('risco.cgu_download.dias', 10));
        $hoje = Carbon::now((string) config('risco.sweep.timezone', 'America/Sao_Paulo'))->startOfDay();

        for ($i = 0; $i < $dias; $i++) {
            $dia = $hoje->copy()->subDays($i);
            $resposta = Http::timeout((int) config('risco.cgu_download.timeout', 180))
                ->withHeaders(['User-Agent' => 'Vigilo/1.0 (+dados abertos)'])
                ->get("{$base}/{$fonte->endpoint()}/{$dia->format('Ymd')}");

            if (! $resposta->successful() || ! str_starts_with($resposta->body(), 'PK')) {
                continue; // sem arquivo nesse dia (403/HTML) — tenta o anterior
            }

            $caminho = tempnam(sys_get_temp_dir(), 'cgu-');

            if ($caminho === false || file_put_contents($caminho, $resposta->body()) === false) {
                throw new RuntimeException('não foi possível gravar o arquivo baixado');
            }

            return [$caminho, $dia->toDateString()];
        }

        throw new RuntimeException("nenhum arquivo publicado nos últimos {$dias} dias");
    }

    /** Lê, valida e recarrega a fonte num átomo; devolve "N sanções". */
    private function gravar(FonteSancao $fonte, string $caminho, string $data): string
    {
        $linhas = $this->ler($fonte, $caminho);

        if ($linhas === []) {
            throw new RuntimeException('arquivo sem nenhuma empresa (CNPJ) válida — cópia anterior mantida');
        }

        DB::transaction(function () use ($fonte, $linhas, $data): void {
            DB::table('cgu_sancoes')->where('fonte', $fonte->value)->delete();

            foreach (array_chunk($linhas, 500) as $lote) {
                DB::table('cgu_sancoes')->insert($lote);
            }

            DB::table('cgu_cargas')->updateOrInsert(
                ['fonte' => $fonte->value],
                ['data_arquivo' => $data, 'registros' => count($linhas), 'carregado_em' => now()],
            );
        });

        $this->info("{$fonte->sigla()}: ".count($linhas).' sanções de empresas carregadas (arquivo de '.Carbon::parse($data)->format('d/m/Y').').');

        return count($linhas).' sanções';
    }

    /**
     * Linhas de PJ do CSV (dentro do zip, ou o próprio CSV), já no formato da
     * tabela. Cabeçalho por NOME (o layout da CGU varia entre as fontes).
     *
     * @return list<array<string, mixed>>
     */
    private function ler(FonteSancao $fonte, string $caminho): array
    {
        [$stream, $zip] = $this->abrirCsv($caminho);

        try {
            $header = fgetcsv($stream, 0, ';', '"', '');

            if (! is_array($header)) {
                throw new RuntimeException('CSV vazio');
            }

            // Encoding decidido UMA vez pelo cabeçalho (acentuado em toda fonte):
            // o da CGU vem em Latin-1; um CSV já em UTF-8 passa direto.
            $latin1 = ! mb_check_encoding(implode(';', array_map(fn (?string $h): string => (string) $h, $header)), 'UTF-8');
            $header = array_map(fn (?string $h): string => $this->utf8((string) $h, $latin1), $header);
            $col = $this->colunas($fonte, $header);

            if ($col['doc'] === null) {
                throw new RuntimeException('coluna do CNPJ não encontrada no cabeçalho');
            }

            $linhas = [];

            while (($row = fgetcsv($stream, 0, ';', '"', '')) !== false) {
                $row = array_map(fn (?string $v): string => $this->utf8((string) $v, $latin1), $row);
                $cnpj = preg_replace('/\D/', '', (string) CsvColumns::cell($row, $col['doc'])) ?? '';

                if (strlen($cnpj) !== 14 || strtoupper((string) CsvColumns::cell($row, $col['tipo_pessoa'])) === 'F') {
                    continue; // só pessoa jurídica
                }

                $linhas[] = [
                    'fonte' => $fonte->value,
                    'codigo' => $this->corta(CsvColumns::cell($row, $col['codigo']), 40),
                    'cnpj' => $cnpj,
                    'cnpj_basico' => substr($cnpj, 0, 8),
                    'nome' => CsvColumns::cell($row, $col['razao']) ?? CsvColumns::cell($row, $col['nome']),
                    'categoria' => CsvColumns::cell($row, $col['categoria']),
                    'orgao' => CsvColumns::cell($row, $col['orgao']),
                    'uf_orgao' => $this->corta(CsvColumns::cell($row, $col['uf']), 2),
                    'esfera' => $this->corta(CsvColumns::cell($row, $col['esfera']), 40),
                    'data_inicio' => $this->data(CsvColumns::cell($row, $col['inicio'])),
                    'data_fim' => $this->data(CsvColumns::cell($row, $col['fim'])),
                    'valor_multa' => $this->valor(CsvColumns::cell($row, $col['multa'])),
                    'processo' => $this->corta(CsvColumns::cell($row, $col['processo']), 120),
                ];
            }

            return $linhas;
        } finally {
            fclose($stream);
            $zip?->close();
        }
    }

    /**
     * Índice de cada coluna pelo nome. CEIS/CNEP: cadastro de sancionados (com
     * datas); CEPIM: entidades impedidas (sem datas — vigente enquanto listada).
     *
     * @param  array<int, string>  $header
     * @return array<string, int|null>
     */
    private function colunas(FonteSancao $fonte, array $header): array
    {
        $f = fn (string ...$termos): ?int => CsvColumns::find($header, array_values($termos));

        if ($fonte === FonteSancao::Cepim) {
            return [
                'doc' => $f('cnpj entidade', 'cnpj'),
                'tipo_pessoa' => null,
                'codigo' => $f('numero convenio'),
                'razao' => null,
                'nome' => $f('nome entidade'),
                'categoria' => $f('motivo do impedimento'),
                'orgao' => $f('orgao concedente'),
                'uf' => null,
                'esfera' => null,
                'inicio' => null,
                'fim' => null,
                'multa' => null,
                'processo' => null,
            ];
        }

        return [
            'doc' => $f('cpf ou cnpj do sancionado'),
            'tipo_pessoa' => $f('tipo de pessoa'),
            'codigo' => $f('codigo da sancao'),
            'razao' => $f('razao social - cadastro receita'),
            'nome' => $f('nome do sancionado'),
            'categoria' => $f('categoria da sancao'),
            'orgao' => $f('orgao sancionador'),
            'uf' => $f('uf orgao sancionador'),
            'esfera' => $f('esfera orgao sancionador'),
            'inicio' => $f('data inicio sancao'),
            'fim' => $f('data final sancao'),
            'multa' => $f('valor da multa'),
            'processo' => $f('numero do processo'),
        ];
    }

    /**
     * Abre o CSV para leitura: o 1º .csv dentro do zip, ou o próprio arquivo.
     *
     * @return array{0: resource, 1: ?ZipArchive}
     */
    private function abrirCsv(string $caminho): array
    {
        $zip = new ZipArchive;

        if ($zip->open($caminho, ZipArchive::RDONLY) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $nome = (string) $zip->getNameIndex($i);

                if (str_ends_with(strtolower($nome), '.csv')) {
                    $stream = $zip->getStream($nome);

                    if ($stream !== false) {
                        return [$stream, $zip];
                    }
                }
            }

            $zip->close();

            throw new RuntimeException('zip sem CSV');
        }

        $stream = fopen($caminho, 'rb');

        if ($stream === false) {
            throw new RuntimeException("não foi possível abrir {$caminho}");
        }

        return [$stream, null];
    }

    private function dataDoArquivoLocal(string $caminho): string
    {
        $data = $this->option('data');

        if (is_string($data) && $data !== '') {
            return Carbon::parse($data)->toDateString();
        }

        if (preg_match('/(\d{4})(\d{2})(\d{2})/', basename($caminho), $m) === 1 && checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return "{$m[1]}-{$m[2]}-{$m[3]}";
        }

        return now()->toDateString();
    }

    /** Campo em UTF-8 (o arquivo da CGU vem em Latin-1/Windows-1252). */
    private function utf8(string $value, bool $latin1): string
    {
        $value = trim($value);

        return $latin1 ? mb_convert_encoding($value, 'UTF-8', 'Windows-1252') : $value;
    }

    /** dd/mm/aaaa → AAAA-MM-DD; vazio/inválido → null. */
    private function data(?string $value): ?string
    {
        if ($value === null || preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $value, $m) !== 1 || ! checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
            return null;
        }

        return "{$m[3]}-{$m[2]}-{$m[1]}";
    }

    /** "137260,07" → 137260.07; vazio/ilegível → null. */
    private function valor(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $normalizado = str_replace(',', '.', str_replace('.', '', $value));

        return is_numeric($normalizado) ? (float) $normalizado : null;
    }

    private function corta(?string $value, int $max): ?string
    {
        return $value === null ? null : mb_substr($value, 0, $max);
    }
}
