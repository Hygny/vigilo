<?php

declare(strict_types=1);

namespace App\DTO\Bens;

use App\DTO\Contratos\ContratoPublico;
use App\DTO\Contratos\ContratosReport;
use App\DTO\Graph\Participacao;

/**
 * "Bens e recebíveis" de uma empresa — onde procurar penhora: aeronaves (RAB),
 * participações societárias (quotas/ações) e contratos públicos vigentes
 * (créditos a receber do governo). Cada fonte pode ter falhado à parte: null =
 * não consultado agora (a tela nunca diz "nenhum" por falha).
 */
final readonly class BensReport
{
    /**
     * @param  list<Aeronave>|null  $aeronaves  as primeiras (ativas primeiro)
     * @param  int  $totalAeronaves  todas, inclusive as canceladas e as fora da lista
     * @param  int  $totalAeronavesAtivas  matrícula não cancelada, inclusive as fora da lista
     * @param  list<Participacao>|null  $participacoes  as primeiras (ativas primeiro)
     */
    public function __construct(
        public ?array $aeronaves,
        public ?string $aeronavesDe,
        public int $totalAeronaves,
        public int $totalAeronavesAtivas,
        public ?array $participacoes,
        public int $totalParticipacoes,
        public ContratosReport $contratos,
    ) {}

    /** A lista de aeronaves bateu no teto (há mais do que as mostradas). */
    public function aeronavesTruncadas(): bool
    {
        return $this->aeronaves !== null && $this->totalAeronaves > count($this->aeronaves);
    }

    /** @return list<Aeronave> das listadas */
    public function aeronavesLivres(): array
    {
        return array_values(array_filter($this->aeronaves ?? [], fn (Aeronave $a): bool => $a->livre()));
    }

    /** @return list<ContratoPublico> */
    public function contratosVigentes(): array
    {
        return array_values(array_filter($this->contratos->contratos, fn (ContratoPublico $c): bool => $c->vigente()));
    }

    public function valorContratosVigentes(): float
    {
        return array_sum(array_map(fn (ContratoPublico $c): float => $c->valor() ?? 0.0, $this->contratosVigentes()));
    }
}
