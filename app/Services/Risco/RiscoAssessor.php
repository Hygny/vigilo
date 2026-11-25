<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Risco\RiscoAssessment;
use App\DTO\Risco\RiscoReport;
use App\Enums\NivelRisco;
use App\Models\MonitoredCompany;
use App\Support\SituacaoCadastral;
use Throwable;

/**
 * Avalia o risco de uma empresa num lugar só (sanções CGU + Lista Suja + dívida
 * ativa PGFN + situação cadastral → semáforo + motivo). Fonte única consumida
 * pelo painel de risco, pelo dossiê PDF e pelo perfil público — evita que a
 * regra do semáforo divirja. A carteira consulta as sanções na API da CGU; o
 * perfil público, na cópia local diária ({@see SancoesLocais}) — mesma regra.
 */
final class RiscoAssessor
{
    public function __construct(
        private readonly RiscoService $risco,
        private readonly DirtyListLookup $listaSujaLookup,
        private readonly PgfnLookup $pgfnLookup,
        private readonly SancoesLocais $sancoesLocais,
    ) {}

    /** Empresa da carteira: sanções pela API da CGU (cache de 12 h). */
    public function para(MonitoredCompany $company): RiscoAssessment
    {
        return $this->avaliar($this->risco->para($company->cnpj), $company->cnpj, $company->latestSnapshot?->situacao_cadastral);
    }

    /**
     * Qualquer CNPJ (perfil público): sanções pela cópia local da CGU — sem uma
     * chamada à API por perfil aberto. `$situacao` = rótulo (ATIVA, BAIXADA…).
     */
    public function paraPublico(string $cnpj, ?string $situacao): RiscoAssessment
    {
        // Cópia velha (agendador parado): o "baixo" não pode soar como garantia.
        $ressalva = $this->sancoesLocais->desatualizada() ? ' A cópia das sanções da CGU está desatualizada.' : '';

        return $this->avaliar($this->sancoesLocais->relatorio($cnpj), $cnpj, $situacao, $ressalva);
    }

    /** @param  string  $ressalvaBaixo  acrescentada ao motivo do nível Baixo */
    private function avaliar(RiscoReport $report, string $cnpj, ?string $situacao, string $ressalvaBaixo = ''): RiscoAssessment
    {
        $listaSuja = $this->listaSujaLookup->consta($cnpj);
        $pgfnDisponivel = true;

        try {
            $pgfn = $this->pgfnLookup->para($cnpj);
        } catch (Throwable $e) {
            // Base CNPJ (onde mora a PGFN) fora do ar: sem o sinal, mas nunca
            // "sem dívida" — o motivo do nível Baixo avisa que não deu para checar.
            report($e);
            $pgfn = null;
            $pgfnDisponivel = false;
        }

        $naListaSuja = $listaSuja !== null;
        $temDivida = $pgfn !== null;

        $nivel = null;
        $motivo = null;

        // O semáforo aparece quando há sinal confiável: sanções consultadas, OU
        // um sinal local sempre confiável (Lista Suja / dívida PGFN). Sem nenhum,
        // não dizemos "baixo" — seria enganoso garantir limpo sem ter checado.
        if ($naListaSuja || $temDivida || ($report->configurado && $report->disponivel)) {
            $situacaoNegativa = SituacaoCadastral::isNegative($situacao);

            $nivel = NivelRisco::avaliar($report->temSancaoAtiva(), $report->temSancoes(), $situacaoNegativa, $naListaSuja, $temDivida);
            $motivo = $naListaSuja
                ? 'Consta na Lista Suja do trabalho escravo (MTE).'
                : match ($nivel) {
                    NivelRisco::Alto => 'Consta sanção vigente nos cadastros da CGU.',
                    NivelRisco::Medio => $this->motivoMedio($situacaoNegativa, $situacao, $report->temSancoes(), $temDivida),
                    NivelRisco::Baixo => ($pgfnDisponivel
                        ? 'Empresa ativa, sem sanções nem dívida ativa nas fontes consultadas.'
                        : 'Empresa ativa, sem sanções nas fontes consultadas (dívida ativa indisponível no momento).').$ressalvaBaixo,
                };
        }

        return new RiscoAssessment($nivel, $motivo, $report, $listaSuja, $pgfn, $pgfnDisponivel);
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
