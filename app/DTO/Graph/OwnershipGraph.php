<?php

declare(strict_types=1);

namespace App\DTO\Graph;

/**
 * Grafo societário de uma empresa (Camada 1): a empresa no centro, seus sócios,
 * e as demais empresas de cada sócio (grupo econômico via aresta reversa).
 */
final readonly class OwnershipGraph
{
    /**
     * @param  list<GraphNode>  $nodes
     * @param  list<GraphEdge>  $edges
     */
    public function __construct(
        public string $center,
        public array $nodes,
        public array $edges,
    ) {}

    /**
     * @return array{centro: string, nos: list<array<string, mixed>>, arestas: list<array<string, mixed>>}
     */
    public function toArray(): array
    {
        return [
            'centro' => $this->center,
            'nos' => array_map(fn (GraphNode $n): array => $n->toArray(), $this->nodes),
            'arestas' => array_map(fn (GraphEdge $e): array => $e->toArray(), $this->edges),
        ];
    }
}
