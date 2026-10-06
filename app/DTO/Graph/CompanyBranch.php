<?php

declare(strict_types=1);

namespace App\DTO\Graph;

/**
 * Uma filial (ou a matriz) da MESMA empresa — estabelecimento com o mesmo
 * `cnpj_basico` do centro, só mudando a ordem (0001 = matriz, 0002+ = filiais).
 * Relação **100% certa** (CNPJ completo), ao contrário das ligações por sócio PF
 * (CPF mascarado). Alimenta o painel de filiais do grifo.
 */
final readonly class CompanyBranch
{
    public function __construct(
        public string $cnpj,        // 14 dígitos
        public bool $isMatriz,
        public ?string $nomeFantasia = null,
        public ?string $situacao = null,
        public ?string $municipio = null,
        public ?string $uf = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'cnpj' => $this->cnpj,
            'matriz' => $this->isMatriz,
            'nome_fantasia' => $this->nomeFantasia,
            'situacao' => $this->situacao,
            'municipio' => $this->municipio,
            'uf' => $this->uf,
        ];
    }
}
