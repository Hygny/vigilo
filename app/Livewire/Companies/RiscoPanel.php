<?php

declare(strict_types=1);

namespace App\Livewire\Companies;

use App\Enums\NivelRisco;
use App\Models\MonitoredCompany;
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

    public function render(RiscoService $risco): View
    {
        $report = $risco->para($this->company->cnpj);

        // O semáforo só aparece quando a consulta rodou de fato — sem isso não
        // avaliamos "baixo" (seria enganoso dizer que está limpo sem ter checado).
        $nivel = null;
        $motivo = null;

        if ($report->configurado && $report->disponivel) {
            $situacao = $this->company->latestSnapshot?->situacao_cadastral;
            $situacaoNegativa = SituacaoCadastral::isNegative($situacao);

            $nivel = NivelRisco::avaliar($report->temSancaoAtiva(), $report->temSancoes(), $situacaoNegativa);
            $motivo = match ($nivel) {
                NivelRisco::Alto => 'Consta sanção vigente nos cadastros da CGU.',
                NivelRisco::Medio => $this->motivoMedio($situacaoNegativa, $situacao, $report->temSancoes()),
                NivelRisco::Baixo => 'Empresa ativa e sem sanções nos cadastros consultados.',
            };
        }

        return view('livewire.companies.risco-panel', [
            'report' => $report,
            'nivel' => $nivel,
            'motivo' => $motivo,
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
