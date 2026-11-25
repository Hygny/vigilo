<?php

declare(strict_types=1);

namespace App\DTO\Vigilancia;

use Illuminate\Support\Carbon;

/**
 * Opção pelo Simples Nacional ou pelo MEI (tabela `dados_simples` da Receita).
 * Empresa sem linha na tabela = nunca optou (`optante` false, sem datas).
 */
final readonly class RegimeSimples
{
    public function __construct(
        public bool $optante,
        public ?string $desde = null,
        public ?string $excluidoEm = null,
    ) {}

    public function texto(): string
    {
        if ($this->optante) {
            return $this->desde !== null ? 'Optante desde '.self::br($this->desde) : 'Optante';
        }

        if ($this->excluidoEm !== null) {
            return 'Não optante — excluída em '.self::br($this->excluidoEm)
                .($this->desde !== null ? ' (optou em '.self::br($this->desde).')' : '');
        }

        return 'Não optante';
    }

    private static function br(string $date): string
    {
        return Carbon::parse($date)->format('d/m/Y');
    }
}
