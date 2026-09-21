<?php

declare(strict_types=1);

namespace App\Services\Graph;

use App\DTO\Graph\GraphEdge;
use App\DTO\Graph\GraphNode;
use App\DTO\Graph\OwnershipGraph;
use App\Support\Cnpj;
use App\Support\SituacaoCadastral;
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
    public function __construct(
        private readonly string $connection,
        private readonly int $reverseLimit,
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

            // Aresta reversa: outras empresas em que este sócio aparece. leftJoin
            // (simétrico ao centro): não perde empresa conectada que falte em `empresas`.
            $others = $db->table('socios as s2')
                ->leftJoin('empresas as emp2', 's2.cnpj_basico', '=', 'emp2.cnpj_basico')
                ->where('s2.cnpj_cpf_do_socio', $document)
                ->where('s2.cnpj_basico', '!=', $basico)
                ->limit($this->reverseLimit)
                ->get(['s2.cnpj_basico', 'emp2.razao_social']);

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
                    document: $otherBasico,
                );
                $edges[$otherId.'|'.$socioId] ??= new GraphEdge($otherId, $socioId, 'socio');
            }
        }

        return new OwnershipGraph($centerId, array_values($nodes), array_values($edges));
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
