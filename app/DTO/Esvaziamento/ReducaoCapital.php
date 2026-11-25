<?php

declare(strict_types=1);

namespace App\DTO\Esvaziamento;

/** Capital social reduzido depois do marco da dívida (histórico cadastral). */
final readonly class ReducaoCapital
{
    public function __construct(
        public float $de,
        public float $para,
        public string $data,
    ) {}
}
