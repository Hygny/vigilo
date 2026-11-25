<?php

declare(strict_types=1);

namespace App\Livewire\Companies;

use App\Jobs\SincronizarDevedorJob;
use App\Models\MonitoredCompany;
use App\Services\Bens\DevedorMonitor;
use App\Support\DataDivida;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

/**
 * Card "Recuperação de crédito" da empresa da carteira: marca/desmarca como
 * DEVEDORA monitorada. Marcada, a varredura diária avisa (Inbox, notificação,
 * webhook) quando surgir aeronave, participação, contrato federal vigente ou
 * possível sucessora NOVOS. A data da dívida é opcional (sem ela, o marco das
 * sucessoras é a 1ª inscrição em dívida ativa da União).
 */
class Recuperacao extends Component
{
    public MonitoredCompany $company;

    /** Campo do formulário (AAAA-MM-DD, opcional). */
    public string $data = '';

    public function mount(MonitoredCompany $company): void
    {
        $this->authorize('view', $company);
        $this->company = $company;
        $this->data = $company->recuperacao_marco?->toDateString() ?? '';
    }

    public function ativar(DevedorMonitor $monitor): void
    {
        $this->authorize('update', $this->company);
        $this->validate(
            ['data' => ['nullable', ...DataDivida::REGRAS]],
            attributes: ['data' => 'data da dívida'],
        );

        // Sob a trava da linha (a mesma do aplicar): outra data refaz só a linha de base das sucessoras.
        $nova = $monitor->marcar($this->company, $this->data !== '' ? Carbon::parse($this->data)->startOfDay() : null);

        SincronizarDevedorJob::dispatch($this->company->id);
        session()->flash('status', $nova ? 'Devedora monitorada: a partir de agora avisamos de bens, créditos e possíveis sucessoras novos.' : 'Monitoramento atualizado.');
    }

    public function desativar(DevedorMonitor $monitor): void
    {
        $this->authorize('update', $this->company);

        $monitor->desmarcar($this->company);
        $this->data = '';
        session()->flash('status', 'Monitoramento de devedora desligado.');
    }

    public function render(): View
    {
        $contagem = $this->company->recuperacaoItens()
            ->selectRaw('tipo, count(*) as total')
            ->groupBy('tipo')
            ->pluck('total', 'tipo')
            ->map(fn ($n): int => (int) $n)
            ->all();

        return view('livewire.companies.recuperacao', ['contagem' => $contagem]);
    }
}
