<?php

declare(strict_types=1);

namespace App\DTO\Risco;

use App\Enums\NivelRisco;
use App\Models\DirtyListEmployer;
use App\Services\Risco\RiscoAssessor;

/**
 * Avaliação de risco consolidada de uma empresa — o que o painel e o dossiê PDF
 * mostram. Reúne o nível (semáforo), o motivo, o relatório de sanções e a
 * eventual entrada na Lista Suja, para a regra do semáforo viver num lugar só
 * ({@see RiscoAssessor}).
 */
final class RiscoAssessment
{
    public function __construct(
        public readonly ?NivelRisco $nivel,
        public readonly ?string $motivo,
        public readonly RiscoReport $report,
        public readonly ?DirtyListEmployer $listaSuja,
        public readonly ?PgfnDebtSummary $pgfn = null,
    ) {}

    /** O semáforo pôde ser avaliado (houve sinal confiável)? */
    public function avaliado(): bool
    {
        return $this->nivel !== null;
    }
}
