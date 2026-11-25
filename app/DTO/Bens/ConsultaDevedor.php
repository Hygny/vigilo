<?php

declare(strict_types=1);

namespace App\DTO\Bens;

/**
 * O que a devedora tem agora, por tipo de item (aeronave, participação,
 * contrato, sucessora). Tipo null = não comparar nesta rodada (fonte
 * incompleta ou que não se aplica). `falhou` = alguma fonte deu ERRO (base CNPJ
 * fora, timeout) — conta para o disjuntor e a rodada não vale como checagem do
 * dia (a próxima passada tenta de novo). `marco` = data usada para as
 * possíveis sucessoras (se mudar antes de aplicar, elas são descartadas).
 */
final readonly class ConsultaDevedor
{
    /**
     * @param  array<string, array<array-key, array{descricao: string, grave: bool}>|null>  $itens
     */
    public function __construct(
        public array $itens,
        public bool $falhou,
        public ?string $marco,
    ) {}
}
