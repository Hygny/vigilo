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
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * IDs dos alertas marcados para ação em lote. Só alertas "em aberto" são
     * selecionáveis — as ações em lote sempre avançam a partir do estado aberto.
     *
     * @var list<int|string>
     */
    public array $selected = [];

    /** Formulário de "descartar em lote" (motivo único) aberto? */
    public bool $bulkDismissing = false;

    public string $bulkDismissReason = '';

    public function setSeverity(string $severity): void
    {
        $this->severity = $severity;
        $this->clearSelection();
    }

    public function setTriage(string $triage): void
    {
        $this->triage = $triage;
        $this->cancelDismiss();
        $this->clearSelection();
    }

    public function startAnalysis(int $id): void
    {
        if ($this->transition($id, TriageStatus::EmAnalise, from: [TriageStatus::Novo])) {
            session()->flash('status', 'Alerta movido para "Em análise".');
        }
    }

    public function promoteToCase(int $id): void
    {
        if ($this->transition($id, TriageStatus::Caso, from: TriageStatus::openCases())) {
            session()->flash('status', 'Alerta marcado como caso.');
        }
    }

    public function reopen(int $id): void
    {
        if ($this->transition($id, TriageStatus::Novo, from: TriageStatus::resolvedCases(), clearReason: true)) {
            session()->flash('status', 'Alerta reaberto.');
        }
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

        // Só se descarta um alerta em aberto (o método é endpoint — a UI já
        // esconde o botão em alertas resolvidos).
        if (! $event->triage_status->isOpen()) {
            $this->cancelDismiss();

            return;
        }

        $event->update([
            'triage_status' => TriageStatus::Descartado,
            'triage_reason' => $validated['dismissReason'],
            'triaged_at' => Carbon::now(),
            'triaged_by_id' => auth()->id(),
        ]);

        $this->cancelDismiss();
        session()->flash('status', 'Alerta descartado.');
    }

    // --- Ações em lote --------------------------------------------------

    /** Marca/desmarca todos os alertas selecionáveis (em aberto) da página. */
    public function toggleSelectAll(): void
    {
        $selectable = $this->openIds($this->visibleEvents());
        $allSelected = $selectable !== [] && array_diff($selectable, $this->normalizedSelection()) === [];

        $this->selected = $allSelected ? [] : $selectable;
    }

    public function clearSelection(): void
    {
        $this->selected = [];
        $this->cancelBulkDismiss();
    }

    public function bulkStartAnalysis(): void
    {
        $count = $this->transitionSelected([TriageStatus::Novo->value], TriageStatus::EmAnalise);
        $this->afterBulk($count, 'movido(s) para "Em análise"');
    }

    public function bulkPromoteToCase(): void
    {
        $count = $this->transitionSelected(TriageStatus::openValues(), TriageStatus::Caso);
        $this->afterBulk($count, 'marcado(s) como caso');
    }

    /** Abre o formulário do motivo único para descartar a seleção. */
    public function beginBulkDismiss(): void
    {
        if ($this->selected === []) {
            return;
        }

        $this->cancelDismiss(); // fecha o descarte de linha única, se aberto
        $this->bulkDismissing = true;
        $this->bulkDismissReason = '';
        $this->resetErrorBag();
    }

    public function cancelBulkDismiss(): void
    {
        $this->bulkDismissing = false;
        $this->bulkDismissReason = '';
        $this->resetErrorBag();
    }

    public function confirmBulkDismiss(): void
    {
        $validated = $this->validate(
            ['bulkDismissReason' => ['required', 'string', 'min:3', 'max:500']],
            [
                'bulkDismissReason.required' => 'Informe o motivo do descarte.',
                'bulkDismissReason.min' => 'Descreva o motivo (mín. 3 caracteres).',
                'bulkDismissReason.max' => 'O motivo deve ter no máximo 500 caracteres.',
            ],
        );

        $count = $this->transitionSelected(
            TriageStatus::openValues(),
            TriageStatus::Descartado,
            $validated['bulkDismissReason'],
        );

        $this->afterBulk($count, 'descartado(s)');
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
        $events = $this->visibleEvents();
        $selectableIds = $this->openIds($events);

        $allSelected = $selectableIds !== []
            && array_diff($selectableIds, $this->normalizedSelection()) === [];

        return view('livewire.alerts.inbox', [
            'events' => $events,
            'selectableIds' => $selectableIds,
            'allSelected' => $allSelected,
            'selectedCount' => count($this->selected),
        ]);
    }

    /**
     * Aplica uma transição de triagem a um alerta, só se o estado atual estiver
     * entre os de origem permitidos. Cada método público é endpoint: sem o guard,
     * um cliente poderia, ex., reabrir um caso ou promover um descartado,
     * sobrescrevendo triaged_at/triaged_by e apagando a resolução anterior.
     *
     * @param  list<TriageStatus>  $from  estados de origem permitidos
     * @return bool true se a transição foi aplicada (false = ignorada)
     */
    private function transition(int $id, TriageStatus $status, array $from, bool $clearReason = false): bool
    {
        $event = $this->findAuthorized($id);

        if (! in_array($event->triage_status, $from, true)) {
            return false;
        }

        $event->update(array_merge([
            'triage_status' => $status,
            'triaged_at' => Carbon::now(),
            'triaged_by_id' => auth()->id(),
        ], $clearReason ? ['triage_reason' => null] : []));

        return true;
    }

    /**
     * Avança os alertas selecionados que estão num dos estados `$from` para
     * `$to`, num único UPDATE escopado à organização. Retorna quantos mudaram.
     *
     * @param  list<string>  $from  valores de TriageStatus elegíveis
     */
    private function transitionSelected(array $from, TriageStatus $to, ?string $reason = null): int
    {
        // Limita a seleção (vem do cliente) aos alertas de fato selecionáveis na
        // página atual — barra IDs arbitrários/fora da página e o tamanho do IN.
        $ids = array_values(array_intersect(
            $this->normalizedSelection(),
            $this->openIds($this->visibleEvents()),
        ));

        if ($ids === []) {
            return 0;
        }

        $payload = [
            'triage_status' => $to->value,
            'triage_reason' => $reason,
            'triaged_at' => Carbon::now(),
            'triaged_by_id' => auth()->id(),
        ];

        return $this->scopedEvents()
            ->whereIn('id', $ids)
            ->whereIn('triage_status', $from)
            ->update($payload);
    }

    private function afterBulk(int $count, string $suffix): void
    {
        $this->clearSelection();

        session()->flash('status', $count === 0
            ? 'Nenhum alerta elegível na seleção.'
            : "{$count} alerta(s) {$suffix}.");
    }

    /**
     * A página de alertas do filtro atual — fonte única da listagem e da
     * seleção (o "selecionar todos" deriva daqui, nunca de uma query à parte).
     *
     * @return Collection<int, ChangeEvent>
     */
    private function visibleEvents(): Collection
    {
        return $this->filteredEvents()
            ->with('monitoredCompany')
            ->orderByDesc('detected_at')
            ->limit(100)
            ->get();
    }

    /**
     * IDs dos alertas em aberto (= selecionáveis) dentre os eventos visíveis.
     *
     * @param  Collection<int, ChangeEvent>  $events
     * @return list<int>
     */
    private function openIds(Collection $events): array
    {
        $ids = $events
            ->filter(fn (ChangeEvent $e): bool => $e->triage_status->isOpen())
            ->map(fn (ChangeEvent $e): int => (int) $e->id)
            ->all();

        return array_values($ids);
    }

    /**
     * Seleção atual como lista de inteiros (os checkboxes entregam strings).
     *
     * @return list<int>
     */
    private function normalizedSelection(): array
    {
        return array_map('intval', $this->selected);
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
