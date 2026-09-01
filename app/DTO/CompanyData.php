<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Normalized representation of a company's Receita cadastral data, decoupled
 * from any specific provider's response shape.
 */
final readonly class CompanyData
{
    /**
     * @param  list<PartnerData>  $partners
     * @param  array<array-key, mixed>  $raw  The provider's untouched payload (persisted as raw_json).
     */
    public function __construct(
        public string $cnpj,
        public string $razaoSocial,
        public ?string $nomeFantasia,
        public string $situacaoCadastral,
        public ?string $situacaoData,
        public ?string $cnaePrincipal,
        public ?string $porte,
        public ?string $naturezaJuridica,
        public ?string $logradouro,
        public ?string $municipio,
        public ?string $uf,
        public array $partners = [],
        public array $raw = [],
    ) {}
}
