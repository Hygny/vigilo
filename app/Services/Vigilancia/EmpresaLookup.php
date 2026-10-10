<?php

declare(strict_types=1);

namespace App\Services\Vigilancia;

use App\DTO\Vigilancia\Empresa;
use App\DTO\Vigilancia\Endereco;
use App\DTO\Vigilancia\Socio;
use App\Providers\Cnpj\LocalCnpjProvider;
use App\Support\AddressNumber;
use App\Support\NomeSocio;
use App\Support\SituacaoCadastral;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Lê a base CNPJ local (PostgreSQL, conexão `cnpj`) e monta o objeto
 * {@see Empresa} do contrato da API OSINT. É leitura pura, SEM escopo de
 * carteira — serve o workflow externo que consulta CNPJ/endereço/sócio
 * arbitrários. Separado do {@see LocalCnpjProvider}
 * (aquele serve o pipeline snapshot/diff e tem o contrato CompanyData a
 * preservar).
 *
 * CNPJ de 14 díg = cnpj_basico(8) + cnpj_ordem(4) + cnpj_dv(2).
 */
final class EmpresaLookup
{
    /**
     * Teto de estabelecimentos lidos por CEP antes do filtro de número. Um CEP
     * específico tem poucos estabelecimentos; o teto só protege contra CEP
     * "geral" patológico. Ordenamos por abertura desc, então pega-se os mais
     * novos — coerente com a ordenação do contrato.
     */
    private const CANDIDATE_SCAN = 2000;

    /** Ordem do estabelecimento matriz (a empresa, para a busca por sócio). */
    private const ORDEM_MATRIZ = '0001';

    public function __construct(private readonly string $connection) {}

    /**
     * Uma empresa pelo CNPJ (estabelecimento específico). Null = fora da base.
     */
    public function porCnpj(string $cnpj): ?Empresa
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== 14) {
            return null;
        }

        $basico = substr($digits, 0, 8);
        $ordem = substr($digits, 8, 4);
        $dv = substr($digits, 12, 2);

        $db = DB::connection($this->connection);

        $row = $this->baseQuery($db)
            ->where('e.cnpj_basico', $basico)
            ->where('e.cnpj_ordem', $ordem)
            ->where('e.cnpj_dv', $dv)
            ->first($this->columns());

        if ($row === null) {
            return null;
        }

        $e = (array) $row;
        $socios = $this->sociosPorBasico($db, [$basico]);

        return $this->build($e, $digits, $socios[$basico] ?? []);
    }

    /**
     * Empresas no mesmo CEP + número (match por CEP exato + número normalizado;
     * complemento NÃO entra — salas diferentes do mesmo prédio contam). Ordena
     * por abertura desc e corta em `$limite`. CEP só-zeros ("00000000", quando o
     * workflow não tem endereço) devolve lista vazia sem tocar na base.
     *
     * @return list<Empresa>
     */
    public function porEndereco(string $cepDigits, string $numeroRaw, ?string $situacaoCode, int $limite): array
    {
        if (ltrim($cepDigits, '0') === '') {
            return [];
        }

        $db = DB::connection($this->connection);

        $query = $this->baseQuery($db)->where('e.cep', $cepDigits);

        if ($situacaoCode !== null) {
            $query->where('e.situacao_cadastral', $situacaoCode);
        }

        $rows = $query
            ->orderBy('e.data_inicio_atividade', 'desc')
            ->orderBy('e.cnpj_basico') // desempate estável em datas iguais
            ->limit(self::CANDIDATE_SCAN)
            ->get($this->columns());

        $alvo = AddressNumber::normalize($numeroRaw);
        $matched = [];

        foreach ($rows as $row) {
            $e = (array) $row;

            if (AddressNumber::normalize($this->strRaw($e['numero'] ?? null)) !== $alvo) {
                continue;
            }

            $matched[] = $e;

            if (count($matched) >= $limite) {
                break;
            }
        }

        if ($matched === []) {
            return [];
        }

        $basicos = array_values(array_unique(array_map(
            fn (array $e): string => $this->strRaw($e['cnpj_basico'] ?? null),
            $matched,
        )));
        $sociosPorBasico = $this->sociosPorBasico($db, $basicos);

        $empresas = [];

        foreach ($matched as $e) {
            $basico = $this->strRaw($e['cnpj_basico'] ?? null);
            $cnpj = $basico.$this->strRaw($e['cnpj_ordem'] ?? null).$this->strRaw($e['cnpj_dv'] ?? null);
            $empresas[] = $this->build($e, $cnpj, $sociosPorBasico[$basico] ?? []);
        }

        return $empresas;
    }

    /**
     * Empresas (matriz) em que algum sócio casa, por NOME completo normalizado
     * (coluna `socios.nome_norm`). Devolve cada empresa com o QSA completo — a
     * checagem fina de homônimo/parente é do consumidor. Nomes já vêm
     * normalizados (via {@see NomeSocio::norm()}).
     *
     * @param  list<string>  $nomesNorm
     * @return list<Empresa>
     */
    public function porSocio(array $nomesNorm, ?string $situacaoCode, int $limite): array
    {
        if ($nomesNorm === []) {
            return [];
        }

        $db = DB::connection($this->connection);

        // Subquery dos cnpj_basico cujos sócios casam pelo nome: deixa o corte
        // (order by abertura desc + limite) acontecer sobre o conjunto INTEIRO
        // no banco — sem teto arbitrário que distorceria "mais novos primeiro".
        $query = $this->baseQuery($db)
            ->whereIn('e.cnpj_basico', function (Builder $sub) use ($nomesNorm): void {
                $sub->from('socios')->select('cnpj_basico')->whereIn('nome_norm', $nomesNorm);
            })
            ->where('e.cnpj_ordem', self::ORDEM_MATRIZ);

        if ($situacaoCode !== null) {
            $query->where('e.situacao_cadastral', $situacaoCode);
        }

        $rows = $query
            ->orderBy('e.data_inicio_atividade', 'desc')
            ->orderBy('e.cnpj_basico') // desempate estável
            ->limit($limite)
            ->get($this->columns());

        $matched = array_map(fn (object $row): array => (array) $row, $rows->all());

        if ($matched === []) {
            return [];
        }

        $matchedBasicos = array_values(array_unique(array_map(
            fn (array $e): string => $this->strRaw($e['cnpj_basico'] ?? null),
            $matched,
        )));
        $sociosPorBasico = $this->sociosPorBasico($db, $matchedBasicos);

        $empresas = [];

        foreach ($matched as $e) {
            $basico = $this->strRaw($e['cnpj_basico'] ?? null);
            $cnpj = $basico.$this->strRaw($e['cnpj_ordem'] ?? null).$this->strRaw($e['cnpj_dv'] ?? null);
            $empresas[] = $this->build($e, $cnpj, $sociosPorBasico[$basico] ?? []);
        }

        return $empresas;
    }

    /**
     * Query base de estabelecimento + razão social + nome de município + CNAE.
     */
    private function baseQuery(ConnectionInterface $db): Builder
    {
        return $db->table('estabelecimentos as e')
            ->leftJoin('empresas as emp', 'e.cnpj_basico', '=', 'emp.cnpj_basico')
            ->leftJoin('municipios as m', 'e.municipio', '=', 'm.codigo')
            ->leftJoin('cnaes as c', 'e.cnae_fiscal_principal', '=', 'c.codigo');
    }

    /**
     * @return list<string>
     */
    private function columns(): array
    {
        return [
            'e.*',
            'emp.razao_social',
            'm.descricao as municipio_nome',
            'c.descricao as cnae_descricao',
        ];
    }

    /**
     * @param  array<string, mixed>  $e
     * @param  list<Socio>  $socios
     */
    private function build(array $e, string $cnpj, array $socios): Empresa
    {
        return new Empresa(
            cnpj: $cnpj,
            razaoSocial: $this->str($e['razao_social'] ?? null),
            nomeFantasia: $this->str($e['nome_fantasia'] ?? null),
            situacao: SituacaoCadastral::label($e['situacao_cadastral'] ?? null),
            dataSituacao: $this->date($e['data_situacao_cadastral'] ?? null),
            dataAbertura: $this->date($e['data_inicio_atividade'] ?? null),
            cnaeCodigo: $this->str($e['cnae_fiscal_principal'] ?? null),
            cnaeDescricao: $this->str($e['cnae_descricao'] ?? null),
            endereco: new Endereco(
                tipoLogradouro: $this->str($e['tipo_logradouro'] ?? null) ?? '',
                logradouro: $this->str($e['logradouro'] ?? null) ?? '',
                numero: $this->str($e['numero'] ?? null) ?? '',
                complemento: $this->str($e['complemento'] ?? null) ?? '',
                bairro: $this->str($e['bairro'] ?? null) ?? '',
                municipio: $this->str($e['municipio_nome'] ?? null),
                uf: $this->str($e['uf'] ?? null),
                cep: $this->cep($e['cep'] ?? null),
            ),
            socios: $socios,
        );
    }

    /**
     * Sócios de vários cnpj_basico em UMA consulta (evita N+1), agrupados por
     * basico. Ordem estável por nome.
     *
     * @param  list<string>  $basicos
     * @return array<string, list<Socio>>
     */
    private function sociosPorBasico(ConnectionInterface $db, array $basicos): array
    {
        if ($basicos === []) {
            return [];
        }

        $rows = $db->table('socios as s')
            ->leftJoin('qualificacoes_socios as q', 's.qualificacao_do_socio', '=', 'q.codigo')
            ->whereIn('s.cnpj_basico', $basicos)
            ->orderBy('s.nome_socio') // ordem estável p/ o consumidor (a base não garante)
            ->get([
                's.cnpj_basico',
                's.nome_socio',
                's.cnpj_cpf_do_socio',
                's.data_entrada_sociedade',
                'q.descricao as qualificacao',
            ]);

        $porBasico = [];

        foreach ($rows as $row) {
            $s = (array) $row;
            $basico = $this->strRaw($s['cnpj_basico'] ?? null);

            $porBasico[$basico][] = new Socio(
                nome: $this->str($s['nome_socio'] ?? null),
                qualificacao: $this->str($s['qualificacao'] ?? null),
                dataEntrada: $this->date($s['data_entrada_sociedade'] ?? null),
                documentoMascarado: $this->maskDocument($s['cnpj_cpf_do_socio'] ?? null),
            );
        }

        return $porBasico;
    }

    /**
     * Máscara defensiva do documento do sócio: a Receita já deve entregar o CPF
     * de PF mascarado (`***NNNNNN**`), mas se vier um CPF completo (11 dígitos),
     * mascara aqui — LGPD, nunca vazar CPF íntegro. CNPJ (14 díg, público) e
     * valores já mascarados passam inalterados.
     */
    private function maskDocument(mixed $value): ?string
    {
        $raw = $this->str($value);

        if ($raw === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $raw) ?? '';

        if (strlen($digits) === 11) {
            return '***'.substr($digits, 3, 6).'**';
        }

        return $raw;
    }

    private function cep(mixed $value): ?string
    {
        $digits = preg_replace('/\D/', '', is_scalar($value) ? (string) $value : '') ?? '';

        return $digits === '' ? null : $digits;
    }

    private function date(mixed $value): ?string
    {
        $raw = is_scalar($value) ? trim((string) $value) : '';

        if ($raw === '' || $raw === '0' || $raw === '0000-00-00') {
            return null;
        }

        try {
            return Carbon::parse($raw)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
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

    private function strRaw(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
