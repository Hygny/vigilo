<?php

declare(strict_types=1);

namespace App\DTO\Graph;

/**
 * Participação societária: uma empresa em que a empresa consultada aparece como
 * **sócia PJ** (pelo CNPJ completo — relação certa). `cnpj` é o da MATRIZ da
 * investida; situação/UF são as da matriz.
 */
final readonly class Participacao
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
