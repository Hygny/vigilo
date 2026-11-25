<?php

declare(strict_types=1);

namespace App\DTO\Contratos;

/**
 * Resultado da consulta de contratos de uma empresa. Estados como o do risco:
 * não configurado (sem chave da API) / indisponível (a API falhou — nunca "sem
 * contratos" por falha) / ok. `truncado` = leu o teto de páginas e pode haver mais.
 */
final readonly class ContratosReport
{
    /**
     * @param  list<ContratoPublico>  $contratos
     */
    public function __construct(
        public string $cnpj,
        public bool $configurado,
        public bool $disponivel,
        public array $contratos = [],
        public bool $truncado = false,
    ) {}

    public static function naoConfigurado(string $cnpj): self
    {
        return new self($cnpj, false, false);
    }

    public static function indisponivel(string $cnpj): self
    {
        return new self($cnpj, true, false);
    }

    /** @param  list<ContratoPublico>  $contratos */
    public static function ok(string $cnpj, array $contratos, bool $truncado = false): self
    {
        return new self($cnpj, true, true, $contratos, $truncado);
    }

    public function total(): int
    {
        return count($this->contratos);
    }

    public function vigentes(): int
    {
        return count(array_filter($this->contratos, fn (ContratoPublico $c): bool => $c->vigente()));
    }

    /**
     * Os $n contratos em destaque: vigentes primeiro, cada grupo na ordem do
     * relatório (mais recentes primeiro).
     *
     * @return list<ContratoPublico>
     */
    public function destaques(int $n): array
    {
        $vigentes = array_filter($this->contratos, fn (ContratoPublico $c): bool => $c->vigente());
        $outros = array_filter($this->contratos, fn (ContratoPublico $c): bool => ! $c->vigente());

        return array_slice([...array_values($vigentes), ...array_values($outros)], 0, $n);
    }

    public function valorTotal(): float
    {
        return array_sum(array_map(fn (ContratoPublico $c): float => $c->valor() ?? 0.0, $this->contratos));
    }
}
