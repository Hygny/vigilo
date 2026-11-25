<?php

declare(strict_types=1);

namespace App\Livewire\Companies;

use App\DTO\Risco\RiscoAoRedorReport;
use App\Models\MonitoredCompany;
use App\Services\Risco\RiscoAoRedor;
use App\Services\Risco\RiscoAssessor;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Throwable;

/**
 * Painel de risco da empresa: consulta as sanções da CGU (CEIS/CNEP/CEPIM) pelo
 * CNPJ e o "risco ao redor" (empresas ligadas com sinal de risco). Pensado para
 * ser montado com `lazy` na página da empresa — a consulta é HTTP externa e não
 * pode travar o 1º paint. Revalidação do CNPJ e cache ficam no {@see RiscoService}.
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

    public function render(RiscoAssessor $assessor, RiscoAoRedor $aoRedor): View
    {
        $assessment = $assessor->para($this->company);

        return view('livewire.companies.risco-panel', [
            'report' => $assessment->report,
            'nivel' => $assessment->nivel,
            'motivo' => $assessment->motivo,
            'listaSuja' => $assessment->listaSuja,
            'pgfn' => $assessment->pgfn,
            'pgfnDisponivel' => $assessment->pgfnDisponivel,
            'aoRedor' => $this->aoRedor($aoRedor),
        ]);
    }

    /** Empresas ligadas com sinal de risco; null = base CNPJ fora (a seção avisa). */
    private function aoRedor(RiscoAoRedor $aoRedor): ?RiscoAoRedorReport
    {
        try {
            return $aoRedor->para($this->company->cnpj);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
