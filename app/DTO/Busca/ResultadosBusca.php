<?php

declare(strict_types=1);

namespace App\DTO\Busca;

/**
 * Resposta da busca por nome: os resultados (já ranqueados) e se a lista foi
 * cortada — termo genérico demais, com mais candidatos do que lemos do índice:
 * a tela pede para refinar.
 */
final readonly class ResultadosBusca
{
    /**
     * @param  list<ResultadoBusca>  $itens
     * @param  list<string>  $termos  termos efetivamente usados
     */
    public function __construct(
        public array $itens,
        public bool $truncado,
        public array $termos,
    ) {}
}
