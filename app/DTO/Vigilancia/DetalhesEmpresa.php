<?php

declare(strict_types=1);

namespace App\DTO\Vigilancia;

/**
 * Dados cadastrais da EMPRESA (raiz do CNPJ) que o perfil mostra além do
 * estabelecimento: natureza jurídica, porte, capital social e os regimes
 * Simples Nacional / MEI. `simples`/`mei` null = a tabela do Simples não pôde
 * ser lida agora (o perfil diz "indisponível", nunca "não optante" por falha).
 */
final readonly class DetalhesEmpresa
{
    public function __construct(
        public ?string $naturezaCodigo,
        public ?string $naturezaDescricao,
        public ?string $porte,
        public ?float $capitalSocial,
        public ?RegimeSimples $simples = null,
        public ?RegimeSimples $mei = null,
    ) {}

    /** "206-2 — Sociedade Empresária Limitada" (formato da Receita), ou o que houver. */
    public function natureza(): ?string
    {
        $codigo = $this->naturezaCodigo !== null && preg_match('/^\d{4}$/', $this->naturezaCodigo) === 1
            ? substr($this->naturezaCodigo, 0, 3).'-'.substr($this->naturezaCodigo, 3)
            : $this->naturezaCodigo;

        $partes = array_filter([$codigo, $this->naturezaDescricao], fn (?string $p): bool => $p !== null && $p !== '');

        return $partes === [] ? null : implode(' — ', $partes);
    }

    public function porteTexto(): ?string
    {
        return match ($this->porte) {
            'ME' => 'Microempresa (ME)',
            'EPP' => 'Empresa de pequeno porte (EPP)',
            'DEMAIS' => 'Demais',
            default => null,
        };
    }

    public function capitalTexto(): ?string
    {
        return $this->capitalSocial === null ? null : 'R$ '.number_format($this->capitalSocial, 2, ',', '.');
    }
}
