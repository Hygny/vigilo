<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Risco\RiscoAssessment;
use App\Enums\NivelRisco;
use App\Models\MonitoredCompany;
use App\Support\SituacaoCadastral;

/**
 * Avalia o risco de uma empresa num lugar só (sanções CGU + Lista Suja + situação
 * cadastral → semáforo + motivo). Fonte única consumida pelo painel de risco e
 * pelo dossiê PDF — evita que a regra do semáforo divirja entre as telas.
 */
final class RiscoAssessor
{
    public function __construct(
        private readonly RiscoService $risco,
        private readonly DirtyListLookup $listaSujaLookup,
    ) {}

    public function para(MonitoredCompany $company): RiscoAssessment
    {
        $report = $this->risco->para($company->cnpj);
        $listaSuja = $this->listaSujaLookup->consta($company->cnpj);
        $naListaSuja = $listaSuja !== null;

        $nivel = null;
        $motivo = null;

        // O semáforo aparece quando há sinal confiável: a consulta de sanções
        // rodou, OU a empresa consta na Lista Suja (sinal local sempre
        // confiável). Sem nenhum dos dois não dizemos "baixo" — seria enganoso
        // garantir que está limpo sem ter checado as sanções.
        if ($naListaSuja || ($report->configurado && $report->disponivel)) {
            $situacao = $company->latestSnapshot?->situacao_cadastral;
            $situacaoNegativa = SituacaoCadastral::isNegative($situacao);

            $nivel = NivelRisco::avaliar($report->temSancaoAtiva(), $report->temSancoes(), $situacaoNegativa, $naListaSuja);
            $motivo = $naListaSuja
                ? 'Consta na Lista Suja do trabalho escravo (MTE).'
                : match ($nivel) {
                    NivelRisco::Alto => 'Consta sanção vigente nos cadastros da CGU.',
                    NivelRisco::Medio => $this->motivoMedio($situacaoNegativa, $situacao, $report->temSancoes()),
                    NivelRisco::Baixo => 'Empresa ativa e sem sanções nos cadastros consultados.',
                };
        }

        return new RiscoAssessment($nivel, $motivo, $report, $listaSuja);
    }

    /**
     * Motivo do nível Médio: junta os dois sinais possíveis (situação cadastral
     * negativa e/ou sanção já expirada) — podem coexistir, então descreve ambos.
     */
    private function motivoMedio(bool $situacaoNegativa, ?string $situacao, bool $temSancoes): string
    {
        $sinais = [];

        if ($situacaoNegativa) {
            $sinais[] = 'situação cadastral '.$situacao;
        }

        if ($temSancoes) {
            $sinais[] = 'sanção já expirada';
        }

        if ($sinais === []) {
            return 'Sinais de atenção no cadastro.';
        }

        return 'Atenção: '.implode(' e ', $sinais).'.';
    }
}
