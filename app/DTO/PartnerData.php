<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * A single partner (sócio) as returned by a CNPJ data provider.
 *
 * The `documento` is typically a masked CPF (e.g. "***123456**") for natural
 * persons. It is stored only to monitor the client's own portfolio (KYB) —
 * never exported for outreach. See README (LGPD).
 */
final readonly class PartnerData
{
    public function __construct(
        public string $nome,
        public ?string $documento = null,
        public ?string $qualificacao = null,
    ) {}
}
