<?php

declare(strict_types=1);

namespace App\Services\Graph;

use App\DTO\Graph\BeneficialOwner;
use App\DTO\Graph\GraphEdge;
use App\DTO\Graph\GraphNode;
use App\DTO\Graph\OwnershipGraph;
use App\Support\Cnpj;
use App\Support\SituacaoCadastral;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

/**
 * Monta o grafo societário de uma empresa (Camada 1) a partir da base CNPJ
 * local (conexão `cnpj`): a empresa no centro, seus sócios (consulta direta em
 * `socios` por cnpj_basico) e as demais empresas de cada sócio (consulta
 * **reversa** por documento, via idx_socios_documento) — o grupo econômico.
 *
 * Nós são deduplicados por id estável; a expansão reversa é limitada por sócio
 * para não virar um "hairball" quando um sócio participa de muitas empresas.
 */
final class OwnershipGraphService
{
    /** Ordem do estabelecimento matriz na base da Receita (a situação-referência da empresa). */
    private const ORDEM_MATRIZ = '0001';

    public function __construct(
        private readonly string $connection,
        private readonly int $reverseLimit,
        private readonly int $maxDepth = 5,
        private readonly int $maxCompanies = 300,
    ) {}

    public function for(string $cnpj): OwnershipGraph
    {
        $digits = Cnpj::normalize($cnpj); // 14 dígitos (lookup, sem exigir mod-11)
        $basico = substr($digits, 0, 8);
        $ordem = substr($digits, 8, 4);
        $dv = substr($digits, 12, 2);

        $db = DB::connection($this->connection);

        $centerId = 'empresa:'.$basico;

        $center = (array) $db->table('estabelecimentos as e')
            ->leftJoin('empresas as emp', 'e.cnpj_basico', '=', 'emp.cnpj_basico')
            ->where('e.cnpj_basico', $basico)
            ->where('e.cnpj_ordem', $ordem)
            ->where('e.cnpj_dv', $dv)
            ->first(['e.situacao_cadastral', 'emp.razao_social']);

        /** @var array<string, GraphNode> $nodes */
        $nodes = [
            $centerId => new GraphNode(
                id: $centerId,
                type: 'empresa',
                label: $this->str($center['razao_social'] ?? null) ?? $digits,
                document: $digits,
                situacao: $this->situacao($center['situacao_cadastral'] ?? null),
            ),
        ];

        /** @var array<string, GraphEdge> $edges  chaveado por "de|para" (dedup) */
        $edges = [];

        $socios = $db->table('socios as s')
            ->leftJoin('qualificacoes_socios as q', 's.qualificacao_do_socio', '=', 'q.codigo')
            ->where('s.cnpj_basico', $basico)
            ->get(['s.nome_socio', 's.cnpj_cpf_do_socio', 's.identificador_de_socio', 'q.descricao as qualificacao']);

        foreach ($socios as $row) {
            $s = (array) $row;
            [$socioId, $socioNode] = $this->socioNode($s);
            $nodes[$socioId] ??= $socioNode;
            $edges[$centerId.'|'.$socioId] ??= new GraphEdge($centerId, $socioId, 'socio', $this->str($s['qualificacao'] ?? null));

            $document = $this->str($s['cnpj_cpf_do_socio'] ?? null);

            if ($document === null) {
                continue;
            }

            $socioName = $this->str($s['nome_socio'] ?? null);
            // CPF mascarado (PF): os 6 dígitos centrais não são únicos, então a
            // ligação reversa pode casar um xará. CNPJ de sócio PJ é completo/único.
            $masked = str_contains($document, '*');

            // Aresta reversa: outras empresas em que este sócio aparece. leftJoin
            // (simétrico ao centro): não perde empresa conectada que falte em
            // `empresas`. leftJoin na matriz (ordem 0001) traz a situação para o
            // nó do grupo ficar vermelho quando BAIXADA já na visão geral.
            $others = $db->table('socios as s2')
                ->leftJoin('empresas as emp2', 's2.cnpj_basico', '=', 'emp2.cnpj_basico')
                ->leftJoin('estabelecimentos as est2', function (JoinClause $join): void {
                    $join->on('est2.cnpj_basico', '=', 's2.cnpj_basico')
                        ->where('est2.cnpj_ordem', '=', self::ORDEM_MATRIZ);
                })
                ->where('s2.cnpj_cpf_do_socio', $document)
                ->where('s2.cnpj_basico', '!=', $basico)
                ->limit($this->reverseLimit)
                ->get(['s2.cnpj_basico', 's2.nome_socio', 'emp2.razao_social', 'est2.situacao_cadastral']);

            foreach ($others as $otherRow) {
                $o = (array) $otherRow;
                $otherBasico = $this->str($o['cnpj_basico'] ?? null);

                if ($otherBasico === null) {
                    continue;
                }

                $otherId = 'empresa:'.$otherBasico;
                $nodes[$otherId] ??= new GraphNode(
                    id: $otherId,
                    type: 'empresa',
                    label: $this->str($o['razao_social'] ?? null) ?? $otherBasico,
                    document: Cnpj::matrizFromBasico($otherBasico) ?? $otherBasico,
                    situacao: $this->situacao($o['situacao_cadastral'] ?? null),
                );

                // Provável (menor confiança): CPF mascarado + primeiro E último
                // nome divergentes → provável xará. Casar primeiro+último (em vez
                // de nome inteiro) evita marcar como provável a mesma pessoa com
                // grafia diferente (nome do meio abreviado/omitido).
                $probable = $masked && ! $this->sameCoreName($socioName, $this->str($o['nome_socio'] ?? null));

                // Dedup determinístico: uma linha confiável (nome bate) prevalece
                // sobre a provável, independente da ordem das linhas do banco.
                $edgeKey = $otherId.'|'.$socioId;
                if (! isset($edges[$edgeKey])) {
                    $edges[$edgeKey] = new GraphEdge($otherId, $socioId, 'socio', null, $probable);
                } elseif (! $probable && $edges[$edgeKey]->probable) {
                    $edges[$edgeKey] = new GraphEdge($otherId, $socioId, 'socio', null, false);
                }
            }
        }

        return new OwnershipGraph($centerId, array_values($nodes), array_values($edges));
    }

    /**
     * Grafo centrado numa PESSOA (sócio): a pessoa no centro e todas as empresas
     * em que ela aparece como sócia (consulta reversa por documento). O documento
     * é o CPF **mascarado** da Receita (`***NNNNNN**`) — pode, raramente, colidir
     * entre pessoas distintas com os mesmos 6 dígitos centrais.
     */
    public function forPerson(string $document, ?string $name = null): OwnershipGraph
    {
        $db = DB::connection($this->connection);
        $centerId = 'socio:'.$document;

        $query = $db->table('socios as s')
            ->leftJoin('empresas as emp', 's.cnpj_basico', '=', 'emp.cnpj_basico')
            ->leftJoin('estabelecimentos as est', function (JoinClause $join): void {
                $join->on('est.cnpj_basico', '=', 's.cnpj_basico')
                    ->where('est.cnpj_ordem', '=', self::ORDEM_MATRIZ);
            })
            ->where('s.cnpj_cpf_do_socio', $document);

        // Refina por nome quando informado: mesmo mascarado + mesmo nome ≈ a
        // mesma pessoa, reduzindo colisões de CPF mascarado.
        if ($name !== null && $name !== '') {
            $query->where('s.nome_socio', $name);
        }

        $rows = $query
            ->limit($this->reverseLimit)
            ->get(['s.cnpj_basico', 's.nome_socio', 's.identificador_de_socio', 'emp.razao_social', 'est.situacao_cadastral']);

        $name = 'Pessoa';
        $type = 'socio_pf';

        /** @var array<string, GraphNode> $companies */
        $companies = [];
        /** @var array<string, GraphEdge> $edges */
        $edges = [];

        foreach ($rows as $row) {
            $r = (array) $row;
            $name = $this->str($r['nome_socio'] ?? null) ?? $name;
            $type = $this->str($r['identificador_de_socio'] ?? null) === '3' ? 'socio_ext' : 'socio_pf';

            $basico = $this->str($r['cnpj_basico'] ?? null);

            if ($basico === null) {
                continue;
            }

            $companyId = 'empresa:'.$basico;
            $companies[$companyId] ??= new GraphNode(
                id: $companyId,
                type: 'empresa',
                label: $this->str($r['razao_social'] ?? null) ?? $basico,
                document: Cnpj::matrizFromBasico($basico) ?? $basico,
                situacao: $this->situacao($r['situacao_cadastral'] ?? null),
            );
            $edges[$companyId.'|'.$centerId] ??= new GraphEdge($companyId, $centerId, 'socio');
        }

        $center = new GraphNode($centerId, $type, $name, $document);

        return new OwnershipGraph($centerId, array_merge([$center], array_values($companies)), array_values($edges));
    }

    /**
     * Beneficiários finais **estruturais**: sobe a cadeia societária (sócios PJ →
     * seus sócios → …) até as pessoas físicas no topo. Cycle-safe (não revisita
     * uma empresa) e limitado por profundidade e por um teto de empresas
     * visitadas. Sem % — não aplica o critério legal de ≥25%, indica controle
     * por estrutura. PF que aparece em vários caminhos fica com o menor nível.
     *
     * @return list<BeneficialOwner>
     */
    public function beneficialOwners(string $cnpj, ?int $maxDepth = null): array
    {
        $maxDepth = max(1, $maxDepth ?? $this->maxDepth);
        $db = DB::connection($this->connection);
        $startBasico = substr(Cnpj::normalize($cnpj), 0, 8);

        /** @var array<string, true> $visited */
        $visited = [$startBasico => true];
        /** @var array<string, BeneficialOwner> $owners */
        $owners = [];
        /** @var list<array{basico: string, depth: int}> $queue */
        $queue = [['basico' => $startBasico, 'depth' => 1]];
        $budget = $this->maxCompanies;

        while ($queue !== [] && $budget > 0) {
            $node = array_shift($queue);
            $budget--;

            $socios = $db->table('socios')
                ->where('cnpj_basico', $node['basico'])
                ->get(['nome_socio', 'cnpj_cpf_do_socio', 'identificador_de_socio']);

            foreach ($socios as $row) {
                $s = (array) $row;
                $ident = $this->str($s['identificador_de_socio'] ?? null);
                $document = $this->str($s['cnpj_cpf_do_socio'] ?? null);
                $nome = $this->str($s['nome_socio'] ?? null) ?? 'Sócio';

                // Sócio PJ: sobe mais um nível (se houver profundidade e ainda não visitado).
                if ($ident === '1') {
                    if ($document !== null && $node['depth'] < $maxDepth) {
                        $pjBasico = substr($document, 0, 8);
                        if (! isset($visited[$pjBasico])) {
                            $visited[$pjBasico] = true;
                            $queue[] = ['basico' => $pjBasico, 'depth' => $node['depth'] + 1];
                        }
                    }

                    continue;
                }

                // PF ('2') ou estrangeiro ('3') → beneficiário final estrutural.
                $key = $document ?? 'n:'.mb_strtolower($nome);

                if (! isset($owners[$key]) || $owners[$key]->depth > $node['depth']) {
                    $owners[$key] = new BeneficialOwner($nome, $document, $ident === '3' ? 'ext' : 'pf', $node['depth']);
                }
            }
        }

        return array_values($owners);
    }

    /**
     * @param  array<string, mixed>  $s
     * @return array{0: string, 1: GraphNode}
     */
    private function socioNode(array $s): array
    {
        $document = $this->str($s['cnpj_cpf_do_socio'] ?? null);
        $nome = $this->str($s['nome_socio'] ?? null) ?? 'Sócio';
        $type = match ($this->str($s['identificador_de_socio'] ?? null)) {
            '1' => 'socio_pj',
            '3' => 'socio_ext',
            default => 'socio_pf',
        };

        $id = $document !== null ? 'socio:'.$document : 'socio:n:'.mb_strtolower($nome);

        return [$id, new GraphNode($id, $type, $nome, $document)];
    }

    /**
     * Dois nomes são "provavelmente o mesmo sócio" quando o PRIMEIRO e o ÚLTIMO
     * nome batem (após normalizar: maiúsculas, espaços colapsados). Ignora nomes
     * do meio, que a Receita às vezes abrevia/omite entre empresas — assim a
     * mesma pessoa com grafia diferente ("MARIA A. SILVA" × "MARIA APARECIDA
     * SILVA") não vira "provável" à toa, reduzindo falsos positivos. Ainda exige
     * o sobrenome bater, então dois primeiros nomes iguais com sobrenome distinto
     * ("MARIA SILVA" × "MARIA SOUZA") seguem prováveis. Nulo/vazio → false.
     */
    private function sameCoreName(?string $a, ?string $b): bool
    {
        $partsA = $this->nameParts($a);
        $partsB = $this->nameParts($b);

        if ($partsA === [] || $partsB === []) {
            return false;
        }

        $firstMatches = $partsA[0] === $partsB[0];
        $lastMatches = end($partsA) === end($partsB);

        return $firstMatches && $lastMatches;
    }

    /**
     * Normaliza um nome em tokens em maiúsculas (espaços colapsados). Nulo/vazio
     * → lista vazia.
     *
     * @return list<string>
     */
    private function nameParts(?string $name): array
    {
        if ($name === null) {
            return [];
        }

        $normalized = trim((string) preg_replace('/\s+/', ' ', mb_strtoupper($name)));

        return $normalized === '' ? [] : explode(' ', $normalized);
    }

    private function situacao(mixed $code): ?string
    {
        $label = SituacaoCadastral::label($code);

        return $label === '' ? null : $label;
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
