<?php

declare(strict_types=1);

namespace App\Livewire\Alerts;

use App\Enums\Severity;
use App\Enums\TriageStatus;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Models\ChangeEvent;
use App\Models\MonitoredCompany;
use App\Models\Portfolio;
use App\Services\Export\AlertsExcelExport;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.app')]
class Inbox extends Component
{
    use AuthorizesRequests, InteractsWithCurrentOrganization;

    /** Filtro de severidade: 'all' ou um valor de Severity. */
    #[Url]
    public string $severity = 'all';

    /** Filtro de triagem: 'abertos' (novo+em análise), 'descartados', 'casos', 'todos'. */
    #[Url]
    public string $triage = 'abertos';

    /** Alerta cujo formulário de "descartar com motivo" está aberto. */
    public ?int $dismissingId = null;

    public string $dismissReason = '';

    public function setSeverity(string $severity): void
    {
        $this->severity = $severity;
    }

    public function setTriage(string $triage): void
    {
        $this->triage = $triage;
        $this->cancelDismiss();
    }

    public function startAnalysis(int $id): void
    {
        $this->transition($id, TriageStatus::EmAnalise);
        session()->flash('status', 'Alerta movido para "Em análise".');
    }

    public function promoteToCase(int $id): void
    {
        $this->transition($id, TriageStatus::Caso);
        session()->flash('status', 'Alerta marcado como caso.');
    }

    public function reopen(int $id): void
    {
        $this->transition($id, TriageStatus::Novo, clearReason: true);
        session()->flash('status', 'Alerta reaberto.');
    }

    /** Abre o formulário de motivo para descartar um alerta. */
    public function beginDismiss(int $id): void
    {
        $this->findAuthorized($id); // garante escopo/autorização antes de abrir
        $this->dismissingId = $id;
        $this->dismissReason = '';
        $this->resetErrorBag();
    }

    public function cancelDismiss(): void
    {
        $this->dismissingId = null;
        $this->dismissReason = '';
        $this->resetErrorBag();
    }

    public function confirmDismiss(): void
    {
        $validated = $this->validate(
            ['dismissReason' => ['required', 'string', 'min:3', 'max:500']],
            [
                'dismissReason.required' => 'Informe o motivo do descarte.',
                'dismissReason.min' => 'Descreva o motivo (mín. 3 caracteres).',
                'dismissReason.max' => 'O motivo deve ter no máximo 500 caracteres.',
            ],
        );

        if ($this->dismissingId === null) {
            return;
        }

        $event = $this->findAuthorized($this->dismissingId);
        $event->update([
            'triage_status' => TriageStatus::Descartado,
            'triage_reason' => $validated['dismissReason'],
            'triaged_at' => Carbon::now(),
            'triaged_by_id' => auth()->id(),
        ]);

        $this->cancelDismiss();
        session()->flash('status', 'Alerta descartado.');
    }

    /**
     * Exporta em Excel TODOS os alertas do filtro atual (sem o teto de 100 da
     * listagem). Respeita escopo da organização + filtros de severidade/triagem.
     */
    public function export(AlertsExcelExport $export): BinaryFileResponse
    {
        $events = $this->filteredEvents()
            ->with('monitoredCompany')
            ->orderByDesc('detected_at')
            ->get();

        $path = $export->build($events);
        $filename = 'alertas-'.Carbon::now()->format('Y-m-d-His').'.xlsx';

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    public function render(): View
    {
        $events = $this->filteredEvents()
            ->with('monitoredCompany')
            ->orderByDesc('detected_at')
            ->limit(100)
            ->get();

        return view('livewire.alerts.inbox', ['events' => $events]);
    }

    private function transition(int $id, TriageStatus $status, bool $clearReason = false): void
    {
        $event = $this->findAuthorized($id);

        $event->update(array_merge([
            'triage_status' => $status,
            'triaged_at' => Carbon::now(),
            'triaged_by_id' => auth()->id(),
        ], $clearReason ? ['triage_reason' => null] : []));
    }

    private function findAuthorized(int $id): ChangeEvent
    {
        $event = $this->scopedEvents()->findOrFail($id);
        $this->authorize('view', $event->monitoredCompany);

        return $event;
    }

    /**
     * Alertas do filtro de severidade + triagem atual.
     *
     * @return Builder<ChangeEvent>
     */
    private function filteredEvents(): Builder
    {
        $query = $this->scopedEvents();

        if ($this->triage === 'abertos') {
            $query->open();
        } elseif ($this->triage === 'descartados') {
            $query->where('triage_status', TriageStatus::Descartado->value);
        } elseif ($this->triage === 'casos') {
            $query->where('triage_status', TriageStatus::Caso->value);
        }

        $severities = array_map(fn (Severity $s): string => $s->value, Severity::cases());
        if (in_array($this->severity, $severities, true)) {
            $query->where('severity', $this->severity);
        }

        return $query;
    }

    /**
     * Change events belonging to the current organization only.
     *
     * @return Builder<ChangeEvent>
     */
    private function scopedEvents(): Builder
    {
        $this->currentOrganizationId();

        $companyIds = MonitoredCompany::query()
            ->whereIn('portfolio_id', Portfolio::query()->pluck('id'))
            ->pluck('id');

        return ChangeEvent::query()->whereIn('monitored_company_id', $companyIds);
    }
}
