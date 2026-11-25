<?php

declare(strict_types=1);

namespace App\Services\Bens;

use App\DTO\Bens\Aeronave;
use App\DTO\Bens\BensReport;
use App\Services\Contratos\ContratosPublicos;
use App\Services\Graph\OwnershipGraphService;
use App\Support\Tentar;

/**
 * Junta, para uma empresa, os bens e recebíveis que dados abertos mostram:
 * aeronaves (cópia local do RAB/ANAC — vínculo certo pelo CNPJ), participações
 * societárias (base CNPJ) e contratos públicos federais vigentes (Portal da
 * Transparência, com cache). Cada fonte degrada sozinha.
 */
final class BensRecebiveis
{
    /** Participações listadas no card (o total vem à parte). */
    private const PARTICIPACOES = 10;

    public function __construct(
        private readonly AeronavesLookup $aeronaves,
        private readonly OwnershipGraphService $grafo,
        private readonly ContratosPublicos $contratos,
    ) {}

    public function daEmpresa(string $cnpj): BensReport
    {
        $aeronaves = Tentar::ouNull(fn (): ?array => $this->aeronaves->disponivel() ? $this->aeronaves->daEmpresa($cnpj) : null);
        $participacoes = Tentar::ouNull(fn (): array => $this->grafo->participations($cnpj, self::PARTICIPACOES));

        // Abaixo do teto a lista é o total; no teto, conta à parte.
        $daLista = fn (array $lista): array => [
            'total' => count($lista),
            'ativas' => count(array_filter($lista, fn (Aeronave $a): bool => $a->ativa())),
        ];
        $contagem = match (true) {
            $aeronaves === null => ['total' => 0, 'ativas' => 0],
            count($aeronaves) < AeronavesLookup::LIMITE => $daLista($aeronaves),
            default => Tentar::ouNull(fn (): array => $this->aeronaves->contagemDaEmpresa($cnpj)) ?? $daLista($aeronaves),
        };

        $totalParticipacoes = match (true) {
            $participacoes === null => 0,
            count($participacoes) < self::PARTICIPACOES => count($participacoes),
            default => Tentar::ouNull(fn (): int => $this->grafo->participationCount($cnpj)) ?? count($participacoes),
        };

        return new BensReport(
            aeronaves: $aeronaves,
            aeronavesDe: $aeronaves !== null ? Tentar::ouNull(fn (): ?string => $this->aeronaves->referencia()) : null,
            totalAeronaves: $contagem['total'],
            totalAeronavesAtivas: $contagem['ativas'],
            participacoes: $participacoes,
            totalParticipacoes: $totalParticipacoes,
            contratos: $this->contratos->para($cnpj),
        );
    }
}
