<?php

declare(strict_types=1);

namespace App\DTO\Graph;

/**
 * Uma aresta do grafo: liga uma empresa a um sócio. A direção é sempre
 * empresa → sócio; um sócio com várias arestas de entrada é ponto de conexão do
 * grupo econômico (sócio em comum).
 */
final readonly class GraphEdge
{
    public function __construct(
        public string $from,
        public string $to,
        public string $relation = 'socio',
        public ?string $qualificacao = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'de' => $this->from,
            'para' => $this->to,
            'tipo' => $this->relation,
            'qualificacao' => $this->qualificacao,
        ];
    }
}
