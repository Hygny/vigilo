<?php

declare(strict_types=1);

namespace App\DTO\Risco;

/**
 * Resumo da dívida ativa (PGFN) de uma empresa: quantas inscrições e o total
 * consolidado. Usado pelo semáforo, pelo card e pelo dossiê.
 */
final class PgfnDebtSummary
{
    public function __construct(
        public readonly int $inscricoes,
        public readonly float $total,
    ) {}
}
