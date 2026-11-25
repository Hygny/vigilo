<?php

declare(strict_types=1);

namespace App\DTO\Pessoas;

/** Resultado da busca de pessoas. `truncado` = havia mais ("refine"). */
final readonly class ResultadosPessoas
{
    /**
     * @param  list<PessoaEncontrada>  $itens
     * @param  list<string>  $termos
     */
    public function __construct(
        public array $itens,
        public bool $truncado,
        public array $termos,
    ) {}
}
