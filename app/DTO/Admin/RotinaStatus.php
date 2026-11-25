<?php

declare(strict_types=1);

namespace App\DTO\Admin;

use Illuminate\Support\Carbon;

/**
 * Situação de uma rotina da plataforma para o card "Rotinas" do /admin.
 * `situacao`: ok | falhou | atrasada | nunca | indisponivel.
 */
final readonly class RotinaStatus
{
    public function __construct(
        public string $nome,
        public string $icone,
        public string $situacao,
        public ?Carbon $ultimaEm,
        public ?string $detalhe,
        public string $periodicidade,
    ) {}

    public function rotulo(): string
    {
        return match ($this->situacao) {
            'ok' => 'Ok',
            'falhou' => 'Falhou',
            'atrasada' => 'Atrasada',
            'nunca' => 'Nunca rodou',
            default => 'Indisponível',
        };
    }

    /** Falhou, atrasada ou sem dado — entra no contador "com atenção". */
    public function precisaAtencao(): bool
    {
        return in_array($this->situacao, ['falhou', 'atrasada', 'indisponivel'], true);
    }

    /** Tom do <x-ui.badge>. */
    public function tom(): string
    {
        return match ($this->situacao) {
            'ok' => 'ok',
            'falhou' => 'crit',
            'atrasada' => 'high',
            default => 'muted',
        };
    }
}
