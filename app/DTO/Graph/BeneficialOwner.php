<?php

declare(strict_types=1);

namespace App\DTO\Graph;

/**
 * Beneficiário final estrutural: a pessoa física no topo da cadeia societária,
 * encontrada subindo os sócios PJ até chegar a PF. É "estrutural" — sem a % de
 * participação (que não está na base aberta da Receita), então não aplica o
 * critério legal de ≥25%; indica quem controla pela estrutura.
 */
final readonly class BeneficialOwner
{
    public function __construct(
        public string $name,
        public ?string $document,
        public string $type,  // pf | ext
        public int $depth,    // níveis acima do centro (1 = sócio direto)
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'nome' => $this->name,
            'documento' => $this->document,
            'tipo' => $this->type,
            'nivel' => $this->depth,
        ];
    }
}
