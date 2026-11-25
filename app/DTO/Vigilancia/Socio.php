<?php

declare(strict_types=1);

namespace App\DTO\Vigilancia;

use App\Support\PessoaChave;

/**
 * Sócio do QSA, no formato do contrato da API OSINT. `documentoMascarado` é o
 * CPF/CNPJ como a Receita entrega (CPF de PF vem mascarado — nunca completo).
 */
final readonly class Socio
{
    public function __construct(
        public ?string $nome,
        public ?string $qualificacao,
        public ?string $dataEntrada,
        public ?string $documentoMascarado,
    ) {}

    /**
     * Chave opaca da página da pessoa provável ({@see PessoaChave}) — só para
     * sócio PESSOA FÍSICA (CPF mascarado, com `*`); PJ e estrangeiro → null.
     */
    public function chavePessoa(): ?string
    {
        return $this->nome !== null && $this->documentoMascarado !== null && str_contains($this->documentoMascarado, '*')
            ? PessoaChave::para($this->documentoMascarado, $this->nome)
            : null;
    }

    /**
     * @return array{nome: ?string, qualificacao: ?string, data_entrada: ?string, documento_mascarado: ?string}
     */
    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'qualificacao' => $this->qualificacao,
            'data_entrada' => $this->dataEntrada,
            'documento_mascarado' => $this->documentoMascarado,
        ];
    }
}
