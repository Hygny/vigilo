<?php

declare(strict_types=1);

namespace App\DTO\Graph;

/**
 * Um nó do grafo societário: uma empresa (centro ou do grupo econômico) ou um
 * sócio (PF/PJ/estrangeiro). O `id` é estável e serve de chave de deduplicação.
 */
final readonly class GraphNode
{
    public function __construct(
        public string $id,
        public string $type,      // empresa | socio_pf | socio_pj | socio_ext
        public string $label,
        public ?string $document = null,
        public ?string $situacao = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->type,
            'nome' => $this->label,
            'documento' => $this->document,
            'situacao' => $this->situacao,
        ];
    }
}
