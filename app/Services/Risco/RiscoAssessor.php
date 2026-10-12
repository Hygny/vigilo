<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Risco\RiscoAssessment;
use App\Enums\NivelRisco;
use App\Models\MonitoredCompany;
use App\Support\SituacaoCadastral;

/**
 * Avalia o risco de uma empresa num lugar só (sanções CGU + Lista Suja + dívida
 * ativa PGFN + situação cadastral → semáforo + motivo). Fonte única consumida
 * pelo painel de risco e pelo dossiê PDF — evita que a regra do semáforo divirja.
 */
final class RiscoAssessor
{
    public function __construct(
        private readonly RiscoService $risco,
        private readonly DirtyListLookup $listaSujaLookup,
        private readonly PgfnLookup $pgfnLookup,
    ) {}

    public function para(MonitoredCompany $company): RiscoAssessment
    {
        $report = $this->risco->para($company->cnpj);
        $listaSuja = $this->listaSujaLookup->consta($company->cnpj);
        $pgfn = $this->pgfnLookup->para($company->cnpj);
        $naListaSuja = $listaSuja !== null;
        $temDivida = $pgfn !== null;

        $nivel = null;
        $motivo = null;

        // O semáforo aparece quando há sinal confiável: sanções consultadas, OU
        // um sinal local sempre confiável (Lista Suja / dívida PGFN). Sem nenhum,
        // não dizemos "baixo" — seria enganoso garantir limpo sem ter checado.
        if ($naListaSuja || $temDivida || ($report->configurado && $report->disponivel)) {
            $situacao = $company->latestSnapshot?->situacao_cadastral;
            $situacaoNegativa = SituacaoCadastral::isNegative($situacao);

            $nivel = NivelRisco::avaliar($report->temSancaoAtiva(), $report->temSancoes(), $situacaoNegativa, $naListaSuja, $temDivida);
            $motivo = $naListaSuja
                ? 'Consta na Lista Suja do trabalho escravo (MTE).'
                : match ($nivel) {
                    NivelRisco::Alto => 'Consta sanção vigente nos cadastros da CGU.',
                    NivelRisco::Medio => $this->motivoMedio($situacaoNegativa, $situacao, $report->temSancoes(), $temDivida),
                    NivelRisco::Baixo => 'Empresa ativa, sem sanções nem dívida ativa nas fontes consultadas.',
                };
        }

        return new RiscoAssessment($nivel, $motivo, $report, $listaSuja, $pgfn);
    }

    /**
     * Motivo do nível Médio: junta os sinais possíveis (situação cadastral
     * negativa, sanção já expirada e/ou dívida ativa) — podem coexistir.
     */
    private function motivoMedio(bool $situacaoNegativa, ?string $situacao, bool $temSancoes, bool $temDivida): string
    {
        $sinais = [];

        if ($situacaoNegativa) {
            $sinais[] = 'situação cadastral '.$situacao;
        }

        if ($temSancoes) {
            $sinais[] = 'sanção já expirada';
        }

        if ($temDivida) {
            $sinais[] = 'dívida ativa na PGFN';
        }

        if ($sinais === []) {
            return 'Sinais de atenção no cadastro.';
        }

        return 'Atenção: '.implode(' e ', $sinais).'.';
    }
}
