<?php

declare(strict_types=1);

namespace App\DTO\Risco;

/**
 * Resumo da dívida ativa (PGFN) de uma empresa: quantas inscrições, quantas
 * já ajuizadas (em execução fiscal) e o total consolidado. Usado pelo semáforo,
 * pelo card, pelo dossiê, pelo grafo e pela API OSINT.
 */
final class PgfnDebtSummary
{
    public function __construct(
        public readonly int $inscricoes,
        public readonly float $total,
        public readonly int $ajuizadas = 0,
    ) {}

    /**
     * Formato da API OSINT (`divida_ativa`).
     *
     * @return array{inscricoes: int, ajuizadas: int, valor_total: float}
     */
    public function toArray(): array
    {
        return [
            'inscricoes' => $this->inscricoes,
            'ajuizadas' => $this->ajuizadas,
            'valor_total' => round($this->total, 2),
        ];
    }

    /** "3 inscrições · R$ 12.345,67" — texto curto para tabela/Copiar/PDF. */
    public function resumo(): string
    {
        $inscricoes = $this->inscricoes.' '.($this->inscricoes === 1 ? 'inscrição' : 'inscrições');

        return $inscricoes.' · R$ '.number_format($this->total, 2, ',', '.');
    }
}
