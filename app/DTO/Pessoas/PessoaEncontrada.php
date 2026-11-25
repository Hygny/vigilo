<?php

declare(strict_types=1);

namespace App\DTO\Pessoas;

/**
 * Uma pessoa PROVÁVEL da busca: nome + CPF mascarado (a Receita não publica o
 * CPF inteiro — homônimos com os mesmos 6 dígitos do meio ficam juntos).
 */
final readonly class PessoaEncontrada
{
    public function __construct(
        public string $chave,
        public string $nome,
        public ?string $documento,
        public bool $estrangeiro,
        public int $empresas,
    ) {}
}
