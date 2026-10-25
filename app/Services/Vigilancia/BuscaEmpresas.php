<?php

declare(strict_types=1);

namespace App\Services\Vigilancia;

use App\DTO\Busca\ResultadoBusca;
use App\DTO\Busca\ResultadosBusca;
use App\Support\BuscaNome;
use App\Support\RazaoSocial;
use App\Support\SituacaoCadastral;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Busca empresas por NOME em toda a base CNPJ: razão social (tabela
 * `busca_razao`, devolve a matriz) e nome fantasia (`busca_fantasia`, devolve o
 * estabelecimento). Cada termo vira `nome LIKE '% TERMO%'` sobre a chave
 * normalizada ({@see BuscaNome}) — começo de palavra, atendido pelo índice
 * trigram no Postgres (deploy/cnpj-db/sql/busca.sql).
 *
 * Lê no máximo `candidatos` linhas por fonte (sem ORDER BY no banco: com termo
 * genérico, ordenar milhões de matches custaria caro) e ranqueia em PHP. Se uma
 * fonte encheu o teto — ou se sobrou mais do que o limite exibido —, o
 * resultado vem marcado como truncado ("refine").
 *
 * Base fora do ar / consulta acima do timeout = {@see QueryException}.
 */
final class BuscaEmpresas
{
    /** SQLSTATE do Postgres para statement_timeout estourado. */
    public const SQLSTATE_TIMEOUT = '57014';

    public function __construct(
        private readonly string $connection,
        private readonly int $candidatos = 200,
        private readonly int $timeoutMs = 5000,
    ) {}

    /**
     * @param  string|null  $situacaoCode  código da Receita (02 = ATIVA...), null = todas
     *
     * @throws QueryException
     */
    public function buscar(string $consulta, ?string $uf = null, ?string $situacaoCode = null, int $limite = 50): ResultadosBusca
    {
        $termos = BuscaNome::termos($consulta);

        if ($termos === []) {
            return new ResultadosBusca([], false, []);
        }

        $db = DB::connection($this->connection);

        [$razao, $fantasia] = $this->comTimeout($db, fn (): array => [
            $this->candidatosRazao($db, $termos, $uf, $situacaoCode),
            $this->candidatosFantasia($db, $termos, $uf, $situacaoCode),
        ]);

        $truncado = count($razao) >= $this->candidatos || count($fantasia) >= $this->candidatos;

        // Mesmo CNPJ pelas duas fontes → fica um (a razão social vem primeiro).
        $porCnpj = [];
        foreach ([['razao', $razao], ['fantasia', $fantasia]] as [$fonte, $linhas]) {
            foreach ($linhas as $linha) {
                $cnpj = $this->str($linha['cnpj_basico'] ?? null).$this->str($linha['cnpj_ordem'] ?? null).$this->str($linha['cnpj_dv'] ?? null);
                $porCnpj[$cnpj] ??= ['fonte' => $fonte, 'linha' => $linha];
            }
        }

        $itens = [];
        foreach ($porCnpj as $cnpj => ['fonte' => $fonte, 'linha' => $linha]) {
            // A chave NÃO é aparada: o espaço das pontas marca começo/fim de palavra.
            $chave = is_string($linha['chave'] ?? null) ? $linha['chave'] : '';
            $itens[] = [
                'score' => $this->score($termos, $chave, $linha),
                'chave' => $chave,
                'item' => new ResultadoBusca(
                    cnpj: (string) $cnpj,
                    razaoSocial: RazaoSocial::exibir($this->strOrNull($linha['razao_social'] ?? null)),
                    nomeFantasia: $this->strOrNull($linha['nome_fantasia'] ?? null),
                    situacao: SituacaoCadastral::label($linha['situacao_cadastral'] ?? null),
                    municipio: $this->strOrNull($linha['municipio_nome'] ?? null),
                    uf: $this->strOrNull($linha['uf'] ?? null),
                    dataAbertura: $this->data($linha['data_inicio_atividade'] ?? null),
                    matriz: (string) ($linha['identificador_matriz_filial'] ?? '') === '1',
                    casouPor: $fonte,
                ),
            ];
        }

        // Mais relevante primeiro; empate → nome mais curto (mais "exato"), depois CNPJ.
        usort($itens, fn (array $a, array $b): int => [$b['score'], strlen($a['chave']), $a['item']->cnpj]
            <=> [$a['score'], strlen($b['chave']), $b['item']->cnpj]);

        $limite = max(1, $limite);

        return new ResultadosBusca(
            itens: array_map(fn (array $i): ResultadoBusca => $i['item'], array_slice($itens, 0, $limite)),
            // Cortado no banco (teto de candidatos) OU aqui (mais que o limite exibido).
            truncado: $truncado || count($itens) > $limite,
            termos: $termos,
        );
    }

    /**
     * @param  list<string>  $termos
     * @return list<array<string, mixed>>
     */
    private function candidatosRazao(ConnectionInterface $db, array $termos, ?string $uf, ?string $situacaoCode): array
    {
        $query = $db->table('busca_razao as b')
            ->join('estabelecimentos as e', function (JoinClause $join): void {
                $join->on('e.cnpj_basico', '=', 'b.cnpj_basico')->where('e.identificador_matriz_filial', '=', 1);
            });

        return $this->executar($query, 'b', $termos, $uf, $situacaoCode);
    }

    /**
     * @param  list<string>  $termos
     * @return list<array<string, mixed>>
     */
    private function candidatosFantasia(ConnectionInterface $db, array $termos, ?string $uf, ?string $situacaoCode): array
    {
        $query = $db->table('busca_fantasia as b')
            ->join('estabelecimentos as e', function (JoinClause $join): void {
                $join->on('e.cnpj_basico', '=', 'b.cnpj_basico')
                    ->on('e.cnpj_ordem', '=', 'b.cnpj_ordem')
                    ->on('e.cnpj_dv', '=', 'b.cnpj_dv');
            });

        return $this->executar($query, 'b', $termos, $uf, $situacaoCode);
    }

    /**
     * Termos (LIKE de começo de palavra) + filtros + dados de exibição, cortado
     * em `candidatos`.
     *
     * @param  list<string>  $termos
     * @return list<array<string, mixed>>
     */
    private function executar(Builder $query, string $alias, array $termos, ?string $uf, ?string $situacaoCode): array
    {
        foreach ($termos as $termo) {
            // Termo já normalizado: só A-Z/0-9 — nada de curinga do usuário no LIKE.
            $query->where("{$alias}.nome", 'like', '% '.$termo.'%');
        }

        if ($uf !== null) {
            $query->where('e.uf', $uf);
        }

        if ($situacaoCode !== null) {
            $query->where('e.situacao_cadastral', $situacaoCode);
        }

        $rows = $query
            ->leftJoin('empresas as emp', 'emp.cnpj_basico', '=', 'e.cnpj_basico')
            ->leftJoin('municipios as m', 'm.codigo', '=', 'e.municipio')
            ->limit($this->candidatos)
            ->get([
                'e.cnpj_basico', 'e.cnpj_ordem', 'e.cnpj_dv', 'e.identificador_matriz_filial',
                'e.nome_fantasia', 'e.situacao_cadastral', 'e.uf', 'e.data_inicio_atividade',
                'emp.razao_social', 'm.descricao as municipio_nome', "{$alias}.nome as chave",
            ]);

        return array_values(array_map(fn (object $row): array => (array) $row, $rows->all()));
    }

    /**
     * Relevância: palavra inteira vale mais que só o começo; os termos na ordem
     * digitada (frase) somam; empresa ATIVA desempata para cima.
     *
     * @param  list<string>  $termos
     * @param  array<string, mixed>  $linha
     */
    private function score(array $termos, string $chave, array $linha): int
    {
        $score = 0;

        foreach ($termos as $termo) {
            $score += str_contains($chave, ' '.$termo.' ') ? 3 : 1;
        }

        if (count($termos) > 1 && str_contains($chave, ' '.implode(' ', $termos).' ')) {
            $score += 4;
        }

        if (SituacaoCadastral::label($linha['situacao_cadastral'] ?? null) === 'ATIVA') {
            $score += 1;
        }

        return $score;
    }

    /**
     * Roda as consultas com statement_timeout no Postgres (SET LOCAL só vale na
     * transação); nos demais drivers (testes em SQLite) roda direto.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    private function comTimeout(ConnectionInterface $db, callable $callback): mixed
    {
        if (! $db instanceof Connection || $db->getDriverName() !== 'pgsql' || $this->timeoutMs <= 0) {
            return $callback();
        }

        return $db->transaction(function () use ($db, $callback): mixed {
            $db->statement('SET LOCAL statement_timeout = '.$this->timeoutMs);

            return $callback();
        });
    }

    private function data(mixed $value): ?string
    {
        $raw = $this->strOrNull($value);

        if ($raw === null) {
            return null;
        }

        try {
            return Carbon::parse($raw)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function str(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function strOrNull(mixed $value): ?string
    {
        $value = $this->str($value);

        return $value === '' ? null : $value;
    }
}
