<?php

declare(strict_types=1);

namespace App\DTO\Esvaziamento;

/**
 * Entrada ou saída de sócio da devedora depois do marco da dívida. O documento
 * (CPF mascarado da Receita) serve só para casar com outras empresas — não é
 * exibido. `dono` = sócio/titular; false = só administra (administrador,
 * diretor, conselheiro, presidente, procurador) — numa S.A. o quadro lista a
 * diretoria, cuja troca é rotina e não indício.
 */
final readonly class MovimentoSocio
{
    public function __construct(
        public string $nome,
        public string $documento,
        public ?string $qualificacao,
        public ?string $data,
        public bool $dono = true,
    ) {}
}
