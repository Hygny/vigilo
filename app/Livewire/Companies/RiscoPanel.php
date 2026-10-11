<?php

declare(strict_types=1);

namespace App\Livewire\Companies;

use App\Models\MonitoredCompany;
use App\Services\Risco\RiscoService;
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
        return view('livewire.companies.risco-panel', [
            'report' => $risco->para($this->company->cnpj),
        ]);
    }
}
