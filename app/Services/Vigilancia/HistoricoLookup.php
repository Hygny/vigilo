<?php

declare(strict_types=1);

namespace App\Services\Vigilancia;

use App\DTO\Historico\EventoCadastral;
use App\DTO\Historico\HistoricoCadastral;
use App\Support\Cnpj;
use App\Support\PorteEmpresa;
use App\Support\RazaoSocial;
use App\Support\SituacaoCadastral;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Histórico cadastral de QUALQUER empresa, lido da base CNPJ (conexão `cnpj`,
 * tabela `historico_cadastral` — gravada na VM pelos gatilhos do reimport e
 * pelo pos-reimport: deploy/cnpj-db/sql). Devolve os eventos já legíveis
 * (códigos viram texto: situação, município, CNAE, natureza, qualificação).
 *
 * Escopo de um CNPJ = eventos da EMPRESA (razão social, capital, sócios — sem
 * ordem/dv) + os do PRÓPRIO estabelecimento; filiais irmãs ficam de fora.
 *
 * Base fora do ar = {@see QueryException} (quem chama degrada). Tabela de
 * descrição ausente só faz o evento sair com o código cru.
 */
final class HistoricoLookup
{
    public const LIMITE_PADRAO = 50;

    /** @var array<string, string> */
    private const ROTULOS = [
        'situacao' => 'Situação cadastral',
        'motivo_situacao' => 'Motivo da situação',
        'endereco' => 'Endereço',
        'cnae_principal' => 'CNAE principal',
        'cnae_secundaria' => 'CNAEs secundários',
        'nome_fantasia' => 'Nome fantasia',
        'razao_social' => 'Razão social',
        'natureza_juridica' => 'Natureza jurídica',
        'capital_social' => 'Capital social',
        'porte' => 'Porte',
        'socio_entrada' => 'Entrada de sócio',
        'socio_saida' => 'Saída de sócio',
    ];

    /** Campo → chave do sinal de 12 meses (sempre presente, mesmo zerado). @var array<string, string> */
    private const SINAIS = [
        'endereco' => 'mudancas_endereco',
        'situacao' => 'mudancas_situacao',
        'razao_social' => 'mudancas_razao_social',
        'socio_entrada' => 'entradas_socios',
        'socio_saida' => 'saidas_socios',
    ];

    public function __construct(private readonly string $connection) {}

    /**
     * Histórico do CNPJ (eventos mais recentes primeiro), ou null para CNPJ
     * inválido.
     *
     * @throws QueryException base CNPJ indisponível
     */
    public function para(string $cnpj, int $limite = self::LIMITE_PADRAO, ?Carbon $agora = null): ?HistoricoCadastral
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== Cnpj::LENGTH) {
            return null;
        }

        $db = DB::connection($this->connection);
        $agora ??= Carbon::now();

        $rows = $this->escopo($db, $digits)
            ->orderByDesc('detectado_em')
            ->orderByDesc('id')
            ->limit(max(1, $limite))
            ->get(['cnpj_basico', 'cnpj_ordem', 'cnpj_dv', 'campo', 'valor_anterior', 'valor_novo', 'detectado_em', 'referencia']);

        $eventos = array_values(array_map(fn (object $row): array => (array) $row, $rows->all()));

        $contagens = $this->escopo($db, $digits)
            ->whereIn('campo', array_keys(self::SINAIS))
            ->where('detectado_em', '>=', $agora->copy()->subYear())
            ->groupBy('campo')
            ->get(['campo', DB::raw('count(*) as total')]);

        $sinais = array_fill_keys(array_values(self::SINAIS), 0);
        foreach ($contagens as $row) {
            $r = (array) $row;
            $campo = $this->str($r['campo'] ?? null) ?? '';
            if (isset(self::SINAIS[$campo])) {
                $sinais[self::SINAIS[$campo]] = (int) (is_numeric($r['total'] ?? null) ? $r['total'] : 0);
            }
        }

        $desde = $db->table('historico_execucoes')->where('status', 'ok')->min('referencia');

        return new HistoricoCadastral(
            desde: is_string($desde) ? $desde : null,
            sinais12m: $sinais,
            eventos: $this->formatar($db, $eventos),
        );
    }

    /** Eventos da empresa (sem ordem/dv) + os do próprio estabelecimento. */
    private function escopo(ConnectionInterface $db, string $digits): Builder
    {
        $ordem = substr($digits, 8, 4);
        $dv = substr($digits, 12, 2);

        return $db->table('historico_cadastral')
            ->where('cnpj_basico', substr($digits, 0, 8))
            ->where(function (Builder $q) use ($ordem, $dv): void {
                $q->whereNull('cnpj_ordem')
                    ->orWhere(fn (Builder $q) => $q->where('cnpj_ordem', $ordem)->where('cnpj_dv', $dv));
            });
    }

    /**
     * @param  list<array<string, mixed>>  $eventos
     * @return list<EventoCadastral>
     */
    private function formatar(ConnectionInterface $db, array $eventos): array
    {
        // Códigos a traduzir, coletados de todos os eventos → uma consulta por tabela.
        $codigos = ['municipios' => [], 'cnaes' => [], 'naturezas_juridicas' => [], 'motivos' => [], 'qualificacoes_socios' => []];

        foreach ($eventos as $e) {
            $campo = $this->str($e['campo'] ?? null);

            foreach ([$e['valor_anterior'] ?? null, $e['valor_novo'] ?? null] as $valor) {
                $valor = $this->str($valor);
                if ($valor === null) {
                    continue;
                }

                match ($campo) {
                    'endereco' => $codigos['municipios'][] = $this->str($this->json($valor)['municipio'] ?? null),
                    'cnae_principal' => $codigos['cnaes'][] = $valor,
                    'natureza_juridica' => $codigos['naturezas_juridicas'][] = $valor,
                    'motivo_situacao' => $codigos['motivos'][] = $valor,
                    'socio_entrada', 'socio_saida' => $codigos['qualificacoes_socios'][] = $this->str($this->json($valor)['qualificacao'] ?? null),
                    default => null,
                };
            }
        }

        $descricoes = [];
        foreach ($codigos as $tabela => $lista) {
            $descricoes[$tabela] = $this->descricoes($db, $tabela, $lista);
        }

        return array_map(function (array $e) use ($descricoes): EventoCadastral {
            $campo = $this->str($e['campo'] ?? null) ?? '';
            $ordem = $this->str($e['cnpj_ordem'] ?? null);
            $dv = $this->str($e['cnpj_dv'] ?? null);

            return new EventoCadastral(
                campo: $campo,
                rotulo: self::ROTULOS[$campo] ?? $campo,
                antes: $this->valor($campo, $this->str($e['valor_anterior'] ?? null), $descricoes),
                depois: $this->valor($campo, $this->str($e['valor_novo'] ?? null), $descricoes),
                detectadoEm: $this->data($e['detectado_em'] ?? null),
                referencia: $this->str($e['referencia'] ?? null),
                estabelecimento: ($ordem !== null && $dv !== null) ? ($this->str($e['cnpj_basico'] ?? null) ?? '').$ordem.$dv : null,
            );
        }, $eventos);
    }

    /**
     * Valor de um campo em texto legível.
     *
     * @param  array<string, array<string, string>>  $d  descrições por tabela
     */
    private function valor(string $campo, ?string $valor, array $d): ?string
    {
        if ($valor === null) {
            return null;
        }

        return match ($campo) {
            'situacao' => SituacaoCadastral::label($valor) !== '' ? SituacaoCadastral::label($valor) : $valor,
            'motivo_situacao' => isset($d['motivos'][$valor]) ? $d['motivos'][$valor] : $valor,
            'endereco' => $this->endereco($this->json($valor), $d['municipios']),
            'cnae_principal', 'natureza_juridica' => $this->comDescricao($valor, $d[$campo === 'cnae_principal' ? 'cnaes' : 'naturezas_juridicas'][$valor] ?? null),
            'cnae_secundaria' => implode(', ', array_filter(array_map('trim', explode(',', $valor)))),
            'razao_social' => RazaoSocial::exibir($valor),
            'capital_social' => is_numeric($valor) ? 'R$ '.number_format((float) $valor, 2, ',', '.') : $valor,
            'porte' => PorteEmpresa::label($valor) ?? $valor,
            'socio_entrada', 'socio_saida' => $this->socio($this->json($valor), $d['qualificacoes_socios']),
            default => $valor,
        };
    }

    /**
     * "RUA X, 100, SALA 1 — CENTRO — CURITIBA/PR — CEP 80020-310".
     *
     * @param  array<string, mixed>  $e
     * @param  array<string, string>  $municipios
     */
    private function endereco(array $e, array $municipios): ?string
    {
        $rua = trim(($this->str($e['tipo_logradouro'] ?? null) ?? '').' '.($this->str($e['logradouro'] ?? null) ?? ''));
        $linha = implode(', ', array_filter([$rua, $this->str($e['numero'] ?? null), $this->str($e['complemento'] ?? null)], fn (?string $p): bool => $p !== null && $p !== ''));

        $codigo = $this->str($e['municipio'] ?? null);
        $cidade = $codigo !== null ? ($municipios[$codigo] ?? $codigo) : null;
        $uf = $this->str($e['uf'] ?? null);
        $local = $cidade !== null && $uf !== null ? "{$cidade}/{$uf}" : ($cidade ?? $uf);

        $cep = preg_replace('/\D/', '', $this->str($e['cep'] ?? null) ?? '') ?? '';
        $cep = strlen($cep) === 8 ? 'CEP '.substr($cep, 0, 5).'-'.substr($cep, 5) : null;

        $partes = array_filter([$linha, $this->str($e['bairro'] ?? null), $local, $cep], fn (?string $p): bool => $p !== null && $p !== '');

        return $partes === [] ? null : implode(' — ', $partes);
    }

    /**
     * "NOME (***123456**) — Sócio-Administrador". CPF íntegro (11 díg) nunca sai.
     *
     * @param  array<string, mixed>  $s
     * @param  array<string, string>  $qualificacoes
     */
    private function socio(array $s, array $qualificacoes): string
    {
        $nome = $this->str($s['nome'] ?? null);
        $doc = $this->str($s['documento'] ?? null);
        $digits = preg_replace('/\D/', '', $doc ?? '') ?? '';

        if (strlen($digits) === 11) {
            $doc = '***'.substr($digits, 3, 6).'**';
        }

        $qual = $this->str($s['qualificacao'] ?? null);
        $qual = $qual !== null ? ($qualificacoes[$qual] ?? $qual) : null;

        $texto = ($nome ?? 'Sócio').($doc !== null ? " ({$doc})" : '');

        return $qual !== null ? "{$texto} — {$qual}" : $texto;
    }

    private function comDescricao(string $codigo, ?string $descricao): string
    {
        return $descricao !== null ? "{$codigo} — {$descricao}" : $codigo;
    }

    /**
     * codigo → descricao de uma tabela de referência da base, numa consulta.
     * Tabela ausente/erro = sem descrições (o evento mostra o código).
     *
     * @param  list<string|null>  $codigos
     * @return array<string, string>
     */
    private function descricoes(ConnectionInterface $db, string $tabela, array $codigos): array
    {
        $codigos = array_values(array_unique(array_filter($codigos, fn (?string $c): bool => $c !== null && $c !== '')));

        if ($codigos === []) {
            return [];
        }

        try {
            $rows = $db->table($tabela)->whereIn('codigo', $codigos)->get(['codigo', 'descricao']);
        } catch (QueryException) {
            return [];
        }

        $mapa = [];
        foreach ($rows as $row) {
            $r = (array) $row;
            $codigo = $this->str($r['codigo'] ?? null);
            $descricao = $this->str($r['descricao'] ?? null);
            if ($codigo !== null && $descricao !== null) {
                $mapa[$codigo] = $descricao;
            }
        }

        return $mapa;
    }

    /** @return array<string, mixed> */
    private function json(string $valor): array
    {
        $decoded = json_decode($valor, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function data(mixed $value): string
    {
        try {
            return Carbon::parse(is_scalar($value) ? (string) $value : 'now')->format('Y-m-d');
        } catch (\Throwable) {
            return '';
        }
    }

    private function str(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
