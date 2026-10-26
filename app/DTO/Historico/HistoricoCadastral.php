<?php

declare(strict_types=1);

namespace App\DTO\Historico;

/**
 * Histórico cadastral de uma empresa na base da Receita: desde quando a base
 * guarda histórico (`desde`, AAAA-MM — antes disso "nenhuma mudança" não quer
 * dizer nada), contagens dos últimos 12 meses (sinais de atenção, ex.: muitas
 * trocas de endereço) e os eventos mais recentes.
 */
final readonly class HistoricoCadastral
{
    /** A partir de quantas trocas de endereço em 12 meses o histórico vira sinal de atenção. */
    public const ALERTA_ENDERECO_12M = 3;

    /**
     * @param  array<string, int>  $sinais12m
     * @param  list<EventoCadastral>  $eventos
     */
    public function __construct(
        public ?string $desde,
        public array $sinais12m,
        public array $eventos,
    ) {}

    /** `desde` para exibição: "2026-10" → "10/2026". */
    public function desdeMesAno(): ?string
    {
        if ($this->desde === null || preg_match('/^(\d{4})-(\d{2})$/', $this->desde, $m) !== 1) {
            return $this->desde;
        }

        return $m[2].'/'.$m[1];
    }

    /** Trocas de endereço nos últimos 12 meses (do próprio estabelecimento). */
    public function mudancasEndereco12m(): int
    {
        return $this->sinais12m['mudancas_endereco'] ?? 0;
    }

    /** Endereço trocado muitas vezes no ano — padrão comum em empresa de fachada. */
    public function alertaEndereco(): bool
    {
        return $this->mudancasEndereco12m() >= self::ALERTA_ENDERECO_12M;
    }

    /**
     * @return array{desde: ?string, sinais_12m: array<string, int>, eventos: list<array<string, mixed>>}
     */
    public function toArray(): array
    {
        return [
            'desde' => $this->desde,
            'sinais_12m' => $this->sinais12m,
            'eventos' => array_map(fn (EventoCadastral $e): array => $e->toArray(), $this->eventos),
        ];
    }
}
