<?php

declare(strict_types=1);

namespace App\DTO\Risco;

/**
 * Resultado do enriquecimento de risco de um CNPJ. Carrega as sanções
 * encontradas E o estado da consulta, para a UI degradar com clareza:
 *
 *  - `configurado = false` → não há chave de API no .env (integração inativa);
 *  - `disponivel = false`  → a consulta rodou mas a fonte falhou/indisponível;
 *  - `configurado && disponivel && sancoes === []` → consulta OK, nada consta.
 */
final class RiscoReport
{
    /**
     * @param  list<Sancao>  $sancoes
     */
    private function __construct(
        public readonly string $cnpj,
        public readonly bool $configurado,
        public readonly bool $disponivel,
        public readonly array $sancoes,
    ) {}

    public static function naoConfigurado(string $cnpj): self
    {
        return new self($cnpj, configurado: false, disponivel: false, sancoes: []);
    }

    public static function indisponivel(string $cnpj): self
    {
        return new self($cnpj, configurado: true, disponivel: false, sancoes: []);
    }

    /**
     * @param  list<Sancao>  $sancoes
     */
    public static function ok(string $cnpj, array $sancoes): self
    {
        return new self($cnpj, configurado: true, disponivel: true, sancoes: $sancoes);
    }

    public function temSancoes(): bool
    {
        return $this->sancoes !== [];
    }

    /** Há ao menos uma sanção vigente (não encerrada)? */
    public function temSancaoAtiva(): bool
    {
        foreach ($this->sancoes as $sancao) {
            if ($sancao->estaAtiva()) {
                return true;
            }
        }

        return false;
    }

    public function total(): int
    {
        return count($this->sancoes);
    }
}
