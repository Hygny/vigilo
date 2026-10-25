<?php

declare(strict_types=1);

namespace App\DTO\Busca;

/**
 * Uma empresa encontrada na busca por nome, pronta para a lista: CNPJ do
 * estabelecimento (a matriz, quando o que casou foi a razão social; o próprio
 * estabelecimento, quando foi o nome fantasia), nomes para exibição (CPF de MEI
 * já mascarado), situação e local.
 */
final readonly class ResultadoBusca
{
    public function __construct(
        public string $cnpj,
        public ?string $razaoSocial,
        public ?string $nomeFantasia,
        public string $situacao,
        public ?string $municipio,
        public ?string $uf,
        public ?string $dataAbertura,
        public bool $matriz,
        public string $casouPor, // razao | fantasia
    ) {}
}
