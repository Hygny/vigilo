<?php

declare(strict_types=1);

namespace App\DTO\Pessoas;

/** Empresa (matriz) em que a pessoa provável aparece como sócia. */
final readonly class EmpresaDaPessoa
{
    public function __construct(
        public string $cnpj,
        public string $nome,
        public ?string $situacao = null,
        public ?string $qualificacao = null,
        public ?string $dataEntrada = null,
        public ?string $uf = null,
    ) {}
}
