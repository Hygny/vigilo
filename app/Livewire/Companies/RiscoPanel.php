<?php

declare(strict_types=1);

namespace App\Livewire\Companies;

use App\Enums\NivelRisco;
use App\Models\MonitoredCompany;
use App\Services\Risco\DirtyListLookup;
use App\Services\Risco\RiscoService;
use App\Support\SituacaoCadastral;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

/**
 * Painel de risco da empresa: consulta as sanções da CGU (CEIS/CNEP/CEPIM) pelo
 * CNPJ. Pensado para ser montado com `lazy` na página da empresa — a consulta é
 * HTTP externa e não pode travar o 1º paint. Revalidação do CNPJ e cache ficam
 * no {@see RiscoService}.
 */
class RiscoPanel extends Component
{
    use AuthorizesRequests;

    public MonitoredCompany $company;

    public function mount(MonitoredCompany $company): void
    {
        // Companies aren't globally scoped — a policy é a guarda de tenant.
        $this->authorize('view', $company);
        $this->company = $company;
    }

    /** Esqueleto exibido enquanto o painel (lazy) carrega. */
    public function placeholder(): View
    {
        return view('livewire.companies.risco-panel-placeholder');
    }

    public function render(RiscoService $risco, DirtyListLookup $listaSujaLookup): View
    {
        $report = $risco->para($this->company->cnpj);
        $listaSuja = $listaSujaLookup->consta($this->company->cnpj);
        $naListaSuja = $listaSuja !== null;

        $nivel = null;
        $motivo = null;

        // O semáforo aparece quando há sinal confiável: a consulta de sanções
        // rodou, OU a empresa consta na Lista Suja (sinal local, sempre
        // confiável). Sem nenhum dos dois não dizemos "baixo" — seria enganoso
        // garantir que está limpo sem ter checado as sanções.
        if ($naListaSuja || ($report->configurado && $report->disponivel)) {
            $situacao = $this->company->latestSnapshot?->situacao_cadastral;
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

        return view('livewire.companies.risco-panel', [
            'report' => $report,
            'nivel' => $nivel,
            'motivo' => $motivo,
            'listaSuja' => $listaSuja,
        ]);
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
