<?php

declare(strict_types=1);

namespace App\DTO\Vigilancia;

/**
 * Endereço de um estabelecimento, no formato do contrato da API OSINT. Os
 * campos de texto vêm como string (vazia quando ausente); município/UF/CEP
 * podem ser nulos. CEP só com dígitos.
 */
final readonly class Endereco
{
    public function __construct(
        public string $tipoLogradouro,
        public string $logradouro,
        public string $numero,
        public string $complemento,
        public string $bairro,
        public ?string $municipio,
        public ?string $uf,
        public ?string $cep,
    ) {}

    /**
     * @return array{tipo_logradouro: string, logradouro: string, numero: string, complemento: string, bairro: string, municipio: ?string, uf: ?string, cep: ?string}
     */
    public function toArray(): array
    {
        return [
            'tipo_logradouro' => $this->tipoLogradouro,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'complemento' => $this->complemento,
            'bairro' => $this->bairro,
            'municipio' => $this->municipio,
            'uf' => $this->uf,
            'cep' => $this->cep,
        ];
    }
}
