<?php

declare(strict_types=1);

namespace App\DTO\Graph;

/**
 * Uma aresta do grafo: liga uma empresa a um sócio. A direção é sempre
 * empresa → sócio; um sócio com várias arestas de entrada é ponto de conexão do
 * grupo econômico (sócio em comum).
 *
 * `probable`: ligação de **menor confiança** — casou só pelo CPF mascarado
 * (`***NNNNNN**`, 6 dígitos centrais) e o nome do sócio diverge entre as duas
 * empresas, então pode ser um xará (colisão de máscara), não a mesma pessoa. A
 * tela pinta essas arestas tracejadas/apagadas.
 */
final readonly class GraphEdge
{
    public function __construct(
        public string $from,
        public string $to,
        public string $relation = 'socio',
        public ?string $qualificacao = null,
        public bool $probable = false,
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
            'provavel' => $this->probable,
        ];
    }
}
