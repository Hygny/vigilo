<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTO\Bens\Aeronave;
use App\Models\MaintenanceRun;
use App\Support\NomeSocio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Cópia local do Registro Aeronáutico Brasileiro (ANAC, dados abertos):
 * `dados_aeronaves.csv` (~24 MB, ~35 mil aeronaves, publicado todo dia; 1ª
 * linha "Atualizado em: AAAA-MM-DD", depois o cabeçalho; `;`; UTF-8 com BOM;
 * PROPRIETARIOS/OPERADORES em JSON). Recarga total num átomo, só se o arquivo
 * for novo (`--forcar` recarrega). Nunca troca a cópia boa por um arquivo
 * pequeno/torto: pisos de aeronaves e de vínculos, e teto de JSON inválido —
 * conferidos antes do commit (senão rollback e a cópia anterior fica).
 *
 * Memória: lê e grava em lotes (o arquivo inteiro em RAM passava de 128 MB, o
 * `memory_limit` padrão do container); o download vai direto para o disco.
 *
 * LGPD: de pessoa física guarda SÓ o nome normalizado (busca por nome), % e
 * UF — nem o nome cru, nem o documento da ANAC (`148XXXXXX43`), que mascara
 * dígitos complementares aos da Receita (`***924790**`): guardar os dois
 * permitiria remontar o CPF inteiro.
 */
final class ImportAeronaves extends Command
{
    /** Linhas por INSERT. */
    private const LOTE = 500;

    /** Piso de vínculos por aeronave (no arquivo real, ~1,9: proprietário + operador). */
    private const VINCULOS_POR_AERONAVE_MIN = 0.5;

    /** Teto da fração de células PROPRIETARIOS/OPERADORES com JSON inválido. */
    private const JSON_INVALIDO_MAX = 0.01;

    protected $signature = 'vigilo:aeronaves-anac
        {--arquivo= : CSV local em vez de baixar}
        {--forcar : recarrega mesmo que o arquivo do dia já esteja carregado}';

    protected $description = 'Atualiza a cópia local do Registro Aeronáutico Brasileiro (ANAC) — bens para recuperação de crédito.';

    public function handle(): int
    {
        $inicio = now();
        $local = $this->option('arquivo');

        try {
            $caminho = is_string($local) && $local !== '' ? $local : $this->baixar();
            $resumo = $this->carregar($caminho);
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            Log::warning('vigilo:aeronaves-anac falhou', ['erro' => $e->getMessage()]);
            MaintenanceRun::registrar(MaintenanceRun::KIND_AERONAVES, false, $e->getMessage(), $inicio);

            return self::FAILURE;
        } finally {
            if (isset($caminho) && (! is_string($local) || $local === '')) {
                @unlink($caminho);
            }
        }

        $this->info($resumo);
        MaintenanceRun::registrar(MaintenanceRun::KIND_AERONAVES, true, $resumo, $inicio);

        return self::SUCCESS;
    }

    private function baixar(): string
    {
        $caminho = tempnam(sys_get_temp_dir(), 'rab-');

        if ($caminho === false) {
            throw new RuntimeException('não foi possível criar o arquivo temporário');
        }

        try {
            // Direto para o disco: o corpo (~24 MB) não passa pela memória.
            $resposta = Http::timeout((int) config('bens.aeronaves.timeout', 180))
                ->withHeaders(['User-Agent' => 'Vigilo/1.0 (+dados abertos)'])
                ->sink($caminho)
                ->get((string) config('bens.aeronaves.url'));
        } catch (Throwable $e) {
            @unlink($caminho);

            throw $e;
        }

        if (! $resposta->successful()) {
            @unlink($caminho);

            throw new RuntimeException('download da ANAC falhou (HTTP '.$resposta->status().')');
        }

        return $caminho;
    }

    private function carregar(string $caminho): string
    {
        $fh = fopen($caminho, 'rb');

        if ($fh === false) {
            throw new RuntimeException("não foi possível abrir {$caminho}");
        }

        try {
            $primeira = (string) fgets($fh);
            $data = preg_match('/(\d{4}-\d{2}-\d{2})/', $primeira, $m) === 1 ? $m[1] : now()->toDateString();

            $ultima = DB::table('aeronave_cargas')->orderByDesc('id')->first(['data_arquivo', 'aeronaves']);
            $atual = $ultima->data_arquivo ?? null;
            if (! $this->option('forcar') && is_string($atual) && substr($atual, 0, 10) === $data) {
                return "arquivo de {$data} já carregado";
            }

            $header = fgetcsv($fh, 0, ';', '"', '');
            if (! is_array($header)) {
                throw new RuntimeException('arquivo sem cabeçalho');
            }
            $header = array_map(fn (?string $h): string => strtoupper(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $h) ?? '')), $header);
            $col = array_flip($header);

            foreach (['MARCAS', 'PROPRIETARIOS', 'OPERADORES'] as $obrigatoria) {
                if (! isset($col[$obrigatoria])) {
                    throw new RuntimeException("coluna {$obrigatoria} ausente no cabeçalho");
                }
            }

            // Leitura e gravação em lotes, num átomo: os pisos são conferidos
            // antes do commit — se falharem, rollback e a cópia anterior fica.
            $anterior = (int) ($ultima->aeronaves ?? 0);
            [$nAeronaves, $nVinculos, $nPj] = DB::transaction(fn (): array => $this->gravar($fh, $col, $data, $anterior));
        } finally {
            fclose($fh);
        }

        return "{$nAeronaves} aeronaves, {$nVinculos} vínculos ({$nPj} de empresas) — arquivo de ".date('d/m/Y', (int) strtotime($data));
    }

    /**
     * @param  resource  $fh
     * @param  array<string, int>  $col
     * @param  int  $anterior  aeronaves da última carga (0 = primeira)
     * @return array{int, int, int} aeronaves, vínculos, vínculos de PJ
     */
    private function gravar($fh, array $col, string $data, int $anterior): array
    {
        DB::table('aeronave_vinculos')->delete();
        DB::table('aeronaves')->delete();

        $vistas = [];
        $aeronaves = [];
        $vinculos = [];
        $nVinculos = 0;
        $nPj = 0;
        $celulas = 0;
        $jsonInvalido = 0;

        while (($r = fgetcsv($fh, 0, ';', '"', '')) !== false) {
            $marca = strtoupper(trim((string) ($r[$col['MARCAS']] ?? '')));

            if ($marca === '' || isset($vistas[$marca])) {
                continue;
            }

            $vistas[$marca] = true;
            $aeronaves[] = [
                'marca' => $marca,
                'modelo' => $this->texto($r, $col, 'DS_MODELO', 120),
                'fabricante' => $this->texto($r, $col, 'NM_FABRICANTE', 120),
                'ano_fabricacao' => $this->ano($r, $col),
                'gravame' => $this->texto($r, $col, 'DS_GRAVAME', 120),
                'data_matricula' => $this->data($r, $col, 'DT_MATRICULA'),
                'data_cancelamento' => $this->data($r, $col, 'DT_CANC'),
                'motivo_cancelamento' => $this->texto($r, $col, 'DS_MOTIVO_CANC', 120),
            ];

            foreach (['PROPRIETARIOS' => Aeronave::PAPEL_PROPRIETARIO, 'OPERADORES' => Aeronave::PAPEL_OPERADOR] as $coluna => $papel) {
                $celulas++;
                $pessoas = $this->pessoas((string) ($r[$col[$coluna]] ?? ''));

                if ($pessoas === null) {
                    $jsonInvalido++;

                    continue;
                }

                foreach ($pessoas as $p) {
                    $vinculos[] = ['marca' => $marca, 'papel' => $papel] + $p;
                    $nVinculos++;
                    $nPj += $p['tipo'] === Aeronave::TIPO_PJ ? 1 : 0;
                }
            }

            if (count($aeronaves) >= self::LOTE) {
                DB::table('aeronaves')->insert($aeronaves);
                $aeronaves = [];
            }

            if (count($vinculos) >= self::LOTE) {
                DB::table('aeronave_vinculos')->insert($vinculos);
                $vinculos = [];
            }
        }

        if ($aeronaves !== []) {
            DB::table('aeronaves')->insert($aeronaves);
        }

        if ($vinculos !== []) {
            DB::table('aeronave_vinculos')->insert($vinculos);
        }

        $nAeronaves = count($vistas);
        $this->conferirPisos($nAeronaves, $anterior, $nVinculos, $celulas, $jsonInvalido);

        DB::table('aeronave_cargas')->insert(['data_arquivo' => $data, 'aeronaves' => $nAeronaves, 'carregado_em' => now()]);

        return [$nAeronaves, $nVinculos, $nPj];
    }

    /** Arquivo torto/truncado (ou formato mudado): exceção → rollback, cópia anterior mantida. */
    private function conferirPisos(int $aeronaves, int $anterior, int $vinculos, int $celulas, int $jsonInvalido): void
    {
        $minimo = max(1, (int) config('bens.aeronaves.min_aeronaves', 10000));

        if ($aeronaves < $minimo) {
            throw new RuntimeException("só {$aeronaves} aeronaves lidas (mínimo {$minimo}) — cópia anterior mantida");
        }

        // Relativo à carga anterior: um arquivo parcial (ex.: 12 mil de 35 mil)
        // passaria no piso absoluto. O RAB só cresce devagar; queda grande = arquivo torto.
        $queda = min(1.0, max(0.0, (float) config('bens.aeronaves.queda_maxima', 0.2)));
        if ($anterior > 0 && $aeronaves < $anterior * (1 - $queda)) {
            throw new RuntimeException("só {$aeronaves} aeronaves lidas, contra {$anterior} na carga anterior (queda acima de ".round($queda * 100).'%) — cópia anterior mantida');
        }

        if ($jsonInvalido > $celulas * self::JSON_INVALIDO_MAX) {
            throw new RuntimeException("{$jsonInvalido} de {$celulas} células de proprietários/operadores com JSON inválido (formato mudou?) — cópia anterior mantida");
        }

        if ($vinculos < $aeronaves * self::VINCULOS_POR_AERONAVE_MIN) {
            throw new RuntimeException("só {$vinculos} vínculos para {$aeronaves} aeronaves — cópia anterior mantida");
        }
    }

    /**
     * Proprietários/operadores do JSON da ANAC. PJ = CNPJ de 14 díg e nome
     * (guardados); PF = só o nome normalizado (nem o nome cru, nem o documento
     * mascarado — ver o docblock da classe); "Indisponível" = fora. Célula vazia
     * = ninguém; null = JSON inválido (conta para o teto).
     *
     * @return list<array{tipo: string, cnpj: ?string, cnpj_basico: ?string, nome: ?string, nome_norm: string, percentual: ?float, uf: ?string}>|null
     */
    private function pessoas(string $json): ?array
    {
        if (trim($json) === '') {
            return [];
        }

        $lista = json_decode($json, true);

        if (! is_array($lista)) {
            return null;
        }

        $out = [];

        foreach ($lista as $p) {
            if (! is_array($p)) {
                continue;
            }

            $nome = trim((string) ($p['NOME'] ?? ''));
            $doc = (string) ($p['DOCUMENTO'] ?? '');

            if ($nome === '' || $nome === '0' || str_starts_with($nome, 'Indispon') || str_starts_with($doc, 'Indispon')) {
                continue;
            }

            $digitos = preg_replace('/\D/', '', $doc) ?? '';
            $pj = strlen($digitos) === 14;
            $percentual = str_replace(',', '.', (string) ($p['PERCENTUAL'] ?? ''));
            $uf = strtoupper(trim((string) ($p['UF'] ?? '')));

            $out[] = [
                'tipo' => $pj ? Aeronave::TIPO_PJ : Aeronave::TIPO_PF,
                'cnpj' => $pj ? $digitos : null,
                'cnpj_basico' => $pj ? substr($digitos, 0, 8) : null,
                'nome' => $pj ? mb_substr($nome, 0, 160) : null,
                'nome_norm' => mb_substr(NomeSocio::norm($nome), 0, 160),
                'percentual' => is_numeric($percentual) ? (float) $percentual : null,
                'uf' => preg_match('/^[A-Z]{2}$/', $uf) === 1 ? $uf : null,
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, string|null>  $r
     * @param  array<string, int>  $col
     */
    private function texto(array $r, array $col, string $nome, int $max): ?string
    {
        $v = isset($col[$nome]) ? trim((string) ($r[$col[$nome]] ?? '')) : '';

        return $v === '' ? null : mb_substr($v, 0, $max);
    }

    /**
     * @param  array<int, string|null>  $r
     * @param  array<string, int>  $col
     */
    private function ano(array $r, array $col): ?int
    {
        $v = $this->texto($r, $col, 'NR_ANO_FABRICACAO', 4);

        return $v !== null && ctype_digit($v) && (int) $v > 1900 && (int) $v <= (int) date('Y') + 1 ? (int) $v : null;
    }

    /**
     * Datas da ANAC: AAAA-MM-DD ou DD/MM/AAAA.
     *
     * @param  array<int, string|null>  $r
     * @param  array<string, int>  $col
     */
    private function data(array $r, array $col, string $nome): ?string
    {
        $v = $this->texto($r, $col, $nome, 30);

        if ($v === null) {
            return null;
        }

        if (preg_match('#^(\d{4})-(\d{2})-(\d{2})#', $v, $m) === 1 && checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return "{$m[1]}-{$m[2]}-{$m[3]}";
        }

        if (preg_match('#^(\d{2})/(\d{2})/(\d{4})#', $v, $m) === 1 && checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }

        return null;
    }
}
