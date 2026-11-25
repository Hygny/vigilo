<?php

declare(strict_types=1);

namespace App\DTO\Pessoas;

/**
 * Página de uma pessoa provável: as empresas dela (até o teto) e os homônimos
 * (mesmo nome, outro CPF mascarado — outras pessoas para a Receita).
 */
final readonly class PessoaPerfil
{
    /**
     * @param  list<EmpresaDaPessoa>  $empresas
     * @param  list<PessoaEncontrada>  $homonimos
     */
    public function __construct(
        public string $chave,
        public string $nome,
        public ?string $documento,
        public bool $estrangeiro,
        public array $empresas,
        public int $totalEmpresas,
        public array $homonimos,
    ) {}
}
