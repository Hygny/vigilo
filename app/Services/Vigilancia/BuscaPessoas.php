<?php

declare(strict_types=1);

namespace App\Services\Vigilancia;

use App\DTO\Pessoas\EmpresaDaPessoa;
use App\DTO\Pessoas\PessoaEncontrada;
use App\DTO\Pessoas\PessoaPerfil;
use App\DTO\Pessoas\ResultadosPessoas;
use App\Support\BuscaNome;
use App\Support\Cnpj;
use App\Support\ConsultaComTimeout;
use App\Support\DataReceita;
use App\Support\NomeSocio;
use App\Support\PessoaChave;
use App\Support\RazaoSocial;
use App\Support\SituacaoCadastral;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use PDOException;

/**
 * Busca de PESSOAS (sócios PF e estrangeiros) por nome na base CNPJ — tabela
 * `busca_pessoa` (deploy/cnpj-db/sql/pessoas-montar.sql): uma linha por pessoa
 * PROVÁVEL = (nome, CPF mascarado).
 *
 * Três leituras, nesta ordem de prioridade: o nome IDÊNTICO ao digitado e o
 * nome que COMEÇA com ele (btree `idx_busca_pessoa_nome`, milissegundos — nunca
 * somem atrás do teto de candidatos, mesmo em nome comum), depois cada termo
 * como `nome LIKE '% TERMO%'` (GIN trigram, sem ORDER BY, até `candidatos`).
 * O ranking final é em PHP: idêntico > começa com > o resto; empate → mais
 * empresas.
 *
 * A página da pessoa é pela `chave` opaca (nunca nome/CPF na URL): as empresas
 * = sócios com o MESMO documento mascarado e o MESMO nome. Homônimos = mesmo
 * nome, OUTRO documento.
 *
 * Base fora do ar / consulta acima do timeout = {@see QueryException} ou
 * {@see PDOException} ({@see ConsultaComTimeout}).
 */
final class BuscaPessoas
{
    /** Homônimos listados na página da pessoa. */
    public const MAX_HOMONIMOS = 20;

    private const ORDEM_MATRIZ = '0001';

    /** `identificador_de_socio` da Receita: pessoa física e estrangeiro (1 = PJ, fora da busca). */
    private const SOCIO_PF = '2';

    private const SOCIO_ESTRANGEIRO = '3';

    public function __construct(
        private readonly string $connection,
        private readonly int $candidatos = 200,
        private readonly int $timeoutMs = 5000,
        private readonly int $maxEmpresas = 200,
    ) {}

    /**
     * @throws QueryException|PDOException
     */
    public function buscar(string $consulta, int $limite = 50): ResultadosPessoas
    {
        $termos = BuscaNome::termos($consulta);

        if (count($termos) < 2) {
            return new ResultadosPessoas([], false, $termos);
        }

        $db = DB::connection($this->connection);
        // Chave do nome inteiro digitado, como a da tabela: " MARIA DA SILVA ".
        $exato = BuscaNome::chave($consulta);

        $colunas = ['chave', 'nome', 'nome_exibicao', 'documento', 'tipo', 'empresas'];

        // 1) Idêntico e 2) "começa com o nome digitado" — btree, em consultas
        // SEPARADAS (um OR viraria BitmapOr na ordem física da tabela, e o teto
        // poderia cortar os idênticos). Milissegundos; rodam fora da transação
        // da leitura por termos, para um timeout dela não descartar estes.
        [$identicas, $prefixo] = ConsultaComTimeout::executar($db, $this->timeoutMs, fn (): array => [
            $db->table('busca_pessoa')->select($colunas)->where('nome', $exato)->limit($this->candidatos)->get()->all(),
            $db->table('busca_pessoa')->select($colunas)->where('nome', 'like', $exato.'%')->where('nome', '!=', $exato)->limit($this->candidatos)->get()->all(),
        ]);
        $prioritarias = array_merge($identicas, $prefixo);

        // 3) Cada termo solto (GIN trigram). Nome comuníssimo pode estourar o
        // tempo: se já há idênticos/prefixo, devolve esses (marcado "refine").
        try {
            $porTermos = ConsultaComTimeout::executar($db, $this->timeoutMs, function () use ($db, $termos, $colunas): array {
                $q = $db->table('busca_pessoa')->select($colunas);
                foreach ($termos as $termo) {
                    $q->where('nome', 'like', '% '.$termo.'%');
                }

                return $q->limit($this->candidatos)->get()->all();
            });
            $termosEstourou = false;
        } catch (QueryException $e) {
            if (! ConsultaComTimeout::estourou($e) || $prioritarias === []) {
                throw $e;
            }
            $porTermos = [];
            $termosEstourou = true;
        }

        /** @var array<string, array{score: int, item: PessoaEncontrada}> $itens */
        $itens = [];

        foreach (array_merge($prioritarias, $porTermos) as $row) {
            $l = (array) $row;
            $chave = (string) $l['chave'];

            if (isset($itens[$chave])) {
                continue;
            }

            $nome = (string) $l['nome'];
            $itens[$chave] = [
                'score' => $nome === $exato ? 2 : (str_starts_with($nome, $exato) ? 1 : 0),
                'item' => $this->pessoaEncontrada($l),
            ];
        }

        $itens = array_values($itens);
        usort($itens, fn (array $a, array $b): int => [$b['score'], $b['item']->empresas, $a['item']->nome]
            <=> [$a['score'], $a['item']->empresas, $b['item']->nome]);

        $limite = max(1, $limite);

        return new ResultadosPessoas(
            itens: array_map(fn (array $i): PessoaEncontrada => $i['item'], array_slice($itens, 0, $limite)),
            // Algum teto do banco cheio OU mais itens que o limite exibido.
            truncado: $termosEstourou || count($porTermos) >= $this->candidatos || count($identicas) >= $this->candidatos
                || count($prefixo) >= $this->candidatos || count($itens) > $limite,
            termos: $termos,
        );
    }

    /**
     * A pessoa provável da chave, com as empresas dela e os homônimos. Null =
     * chave inválida/inexistente.
     *
     * @throws QueryException|PDOException
     */
    public function pessoa(string $chave): ?PessoaPerfil
    {
        if (! PessoaChave::valida($chave)) {
            return null;
        }

        $chave = strtolower($chave);
        $db = DB::connection($this->connection);

        return ConsultaComTimeout::executar($db, $this->timeoutMs, function () use ($db, $chave): ?PessoaPerfil {
            $row = $db->table('busca_pessoa')->where('chave', $chave)->first();

            if ($row === null) {
                return null;
            }

            $p = (array) $row;
            $nome = (string) $p['nome_exibicao'];
            $documento = (string) $p['documento'];

            // Mesmo nome, OUTRO documento (a mesma pessoa com grafia variante e o
            // mesmo CPF mascarado não é "homônimo") — btree no nome.
            $homonimos = $db->table('busca_pessoa')
                ->where('nome', (string) $p['nome'])
                ->where('documento', '!=', $documento)
                ->orderByDesc('empresas')
                ->limit(self::MAX_HOMONIMOS)
                ->get(['chave', 'nome', 'nome_exibicao', 'documento', 'tipo', 'empresas']);

            return new PessoaPerfil(
                chave: $chave,
                nome: $nome,
                documento: $this->documento($documento),
                estrangeiro: (string) ($p['tipo'] ?? '') === self::SOCIO_ESTRANGEIRO,
                empresas: $this->empresas($db, $documento, $nome),
                totalEmpresas: (int) $p['empresas'],
                homonimos: array_values(array_map(fn (object $h): PessoaEncontrada => $this->pessoaEncontrada((array) $h), $homonimos->all())),
            );
        });
    }

    /** @param  array<string, mixed>  $l */
    private function pessoaEncontrada(array $l): PessoaEncontrada
    {
        return new PessoaEncontrada(
            chave: (string) $l['chave'],
            nome: (string) $l['nome_exibicao'],
            documento: $this->documento($l['documento'] ?? null),
            estrangeiro: (string) ($l['tipo'] ?? '') === self::SOCIO_ESTRANGEIRO,
            empresas: (int) $l['empresas'],
        );
    }

    /**
     * Empresas em que (documento, nome) aparece como sócio PF/estrangeiro. Pelo
     * índice do documento quando há; sem documento, pelo índice do nome
     * normalizado (`nome_norm`).
     *
     * @return list<EmpresaDaPessoa>
     */
    private function empresas(ConnectionInterface $db, string $documento, string $nome): array
    {
        $q = $db->table('socios as s')
            ->leftJoin('empresas as emp', 's.cnpj_basico', '=', 'emp.cnpj_basico')
            ->leftJoin('estabelecimentos as est', function (JoinClause $join): void {
                $join->on('est.cnpj_basico', '=', 's.cnpj_basico')->where('est.cnpj_ordem', '=', self::ORDEM_MATRIZ);
            })
            ->leftJoin('qualificacoes_socios as q', 's.qualificacao_do_socio', '=', 'q.codigo')
            ->whereIn('s.identificador_de_socio', [self::SOCIO_PF, self::SOCIO_ESTRANGEIRO])
            ->whereRaw('trim(s.nome_socio) = ?', [$nome]);

        // Estrangeiro vem sem documento ou com "00000000000000" (o mesmo para
        // todos): aí o índice seletivo é o do nome normalizado, não o do documento.
        if ($documento !== '' && ltrim($documento, '0') !== '') {
            $q->where('s.cnpj_cpf_do_socio', $documento);
        } else {
            $q->where('s.nome_norm', NomeSocio::norm($nome))
                ->where(fn (Builder $w) => $w->whereNull('s.cnpj_cpf_do_socio')->orWhere('s.cnpj_cpf_do_socio', $documento));
        }

        $rows = $q->orderByRaw('case when est.situacao_cadastral = ? then 0 else 1 end', [SituacaoCadastral::code('ATIVA')])
            ->orderBy('emp.razao_social')
            ->limit($this->maxEmpresas)
            ->get(['s.cnpj_basico', 's.data_entrada_sociedade', 'emp.razao_social', 'est.situacao_cadastral', 'est.uf', 'q.descricao as qualificacao']);

        $out = [];

        foreach ($rows as $row) {
            $r = (array) $row;
            $basico = is_scalar($r['cnpj_basico'] ?? null) ? (string) $r['cnpj_basico'] : '';

            if ($basico === '' || isset($out[$basico])) {
                continue;
            }

            $situacao = SituacaoCadastral::label($r['situacao_cadastral'] ?? null);
            $out[$basico] = new EmpresaDaPessoa(
                cnpj: Cnpj::matrizFromBasico($basico) ?? $basico,
                nome: RazaoSocial::exibir(is_string($r['razao_social'] ?? null) ? $r['razao_social'] : null) ?? $basico,
                situacao: $situacao === '' ? null : $situacao,
                qualificacao: is_string($r['qualificacao'] ?? null) ? $r['qualificacao'] : null,
                dataEntrada: DataReceita::ymd($r['data_entrada_sociedade'] ?? null),
                uf: is_string($r['uf'] ?? null) ? $r['uf'] : null,
            );
        }

        return array_values($out);
    }

    /**
     * CPF mascarado como vem ('***NNNNNN**'); 11 dígitos crus (defensivo) →
     * mascara; vazio ou só zeros (o "documento" do estrangeiro na base) → null.
     */
    private function documento(mixed $value): ?string
    {
        $raw = is_scalar($value) ? trim((string) $value) : '';
        $digits = preg_replace('/\D/', '', $raw) ?? '';

        if ($raw === '' || ($digits !== '' && ltrim($digits, '0') === '' && ! str_contains($raw, '*'))) {
            return null;
        }

        return strlen($digits) === 11 && ! str_contains($raw, '*') ? '***'.substr($digits, 3, 6).'**' : $raw;
    }
}
