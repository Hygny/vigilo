<?php

declare(strict_types=1);

namespace App\DTO\Vigilancia;

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
