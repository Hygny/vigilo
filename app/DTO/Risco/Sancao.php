<?php

declare(strict_types=1);

namespace App\DTO\Risco;

use App\Enums\FonteSancao;

/**
 * Uma sanção encontrada num cadastro da CGU (CEIS/CNEP/CEPIM) amarrada a um
 * CNPJ consultado. Campos são best-effort — a resposta do Portal é aninhada e
 * varia por cadastro, então tudo que não vier fica null.
 */
final class Sancao
{
    public function __construct(
        public readonly FonteSancao $fonte,
        public readonly ?string $tipo,
        public readonly ?string $orgao,
        public readonly ?string $dataInicio,
        public readonly ?string $dataFim,
        public readonly ?string $fundamentacao,
    ) {}

    /**
     * @return array{fonte: string, sigla: string, tipo: ?string, orgao: ?string, data_inicio: ?string, data_fim: ?string, fundamentacao: ?string}
     */
    public function toArray(): array
    {
        return [
            'fonte' => $this->fonte->value,
            'sigla' => $this->fonte->sigla(),
            'tipo' => $this->tipo,
            'orgao' => $this->orgao,
            'data_inicio' => $this->dataInicio,
            'data_fim' => $this->dataFim,
            'fundamentacao' => $this->fundamentacao,
        ];
    }
}
