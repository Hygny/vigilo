<?php

declare(strict_types=1);

namespace App\Livewire\Portfolios;

use App\Actions\QueuePortfolioRefresh;
use App\Enums\RefreshStatus;
use App\Jobs\RefreshMonitoredCompanyJob;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Models\MonitoredCompany;
use App\Models\Portfolio;
use App\Services\CompanyImporter;
use App\Support\Cnpj;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Show extends Component
{
    use AuthorizesRequests, InteractsWithCurrentOrganization, WithFileUploads, WithPagination;

    public Portfolio $portfolio;

    #[Validate('required|string')]
    public string $cnpj = '';

    #[Validate('nullable|string|max:255')]
    public ?string $label = null;

    #[Validate('nullable|file|max:5120')]
    public ?TemporaryUploadedFile $csv = null;

    /**
     * @var array{imported: int, rejected: list<array{line: int, value: string, reason: string}>}|null
     */
    public ?array $importReport = null;

    /** Filtro por status da coleta: all|pending|ok|nao_encontrado|erro. */
    #[Url]
    public string $statusFilter = 'all';

    /** Rótulos dos filtros, na ordem em que aparecem na UI. */
    private const FILTERS = [
        'all' => 'Todos',
        'pending' => 'Pendentes',
        'ok' => 'Atualizados',
        'nao_encontrado' => 'Não encontrados',
        'erro' => 'Erros',
    ];

    /**
     * Dias do mês (1–31) selecionados para o agendamento mensal do portfólio.
     *
     * @var list<int>
     */
    public array $scheduleDays = [];

    public function mount(Portfolio $portfolio): void
    {
        $this->authorize('view', $portfolio);
        $this->portfolio = $portfolio;
        $this->scheduleDays = $this->normalizedScheduleDays($portfolio->schedule_days ?? []);
    }

    public function toggleScheduleDay(int $day): void
    {
        if ($day < 1 || $day > 31) {
            return;
        }

        $days = in_array($day, $this->scheduleDays, true)
            ? array_diff($this->scheduleDays, [$day])
            : [...$this->scheduleDays, $day];

        $this->scheduleDays = $this->normalizedScheduleDays($days);
    }

    /**
     * Aplica um preset comum de agendamento. Não persiste: o usuário confirma
     * com "Salvar".
     */
    public function applyPreset(string $preset): void
    {
        $this->scheduleDays = match ($preset) {
            'day1' => [1],
            'day15' => [15],
            'biweekly' => [1, 15],
            'lastday' => [31],
            default => $this->scheduleDays,
        };
    }

    public function clearScheduleDays(): void
    {
        $this->scheduleDays = [];
    }

    /**
     * Descarta alterações não salvas, voltando ao agendamento persistido.
     */
    public function discardScheduleChanges(): void
    {
        $this->scheduleDays = $this->normalizedScheduleDays($this->portfolio->schedule_days ?? []);
    }

    public function saveSchedule(): void
    {
        $this->authorize('update', $this->portfolio);

        $days = $this->normalizedScheduleDays($this->scheduleDays);

        $this->portfolio->update(['schedule_days' => $days === [] ? null : $days]);
        $this->scheduleDays = $days;

        session()->flash('status', $days === []
            ? 'Agendamento removido.'
            : 'Agendamento salvo: dia(s) '.implode(', ', $days).' de cada mês.');
    }

    /**
     * Sanitiza a lista de dias (o cliente pode setar $scheduleDays direto):
     * só 1–31, sem repetição, ordenada e reindexada como lista.
     *
     * @param  array<array-key, int|string>  $days
     * @return list<int>
     */
    private function normalizedScheduleDays(array $days): array
    {
        $set = [];
        foreach ($days as $value) {
            $day = (int) $value;
            if ($day >= 1 && $day <= 31) {
                $set[$day] = $day;
            }
        }
        ksort($set);

        return array_values($set);
    }

    /**
     * Próxima data em que o agendamento salvo dispara — espelha a regra de
     * RunDuePortfolioSchedules (um dia > dias-do-mês escorrega para o último).
     * Retorna null se não há agendamento.
     *
     * @param  list<int>  $days
     */
    private function nextScheduledRun(array $days): ?CarbonImmutable
    {
        if ($days === []) {
            return null;
        }

        $today = CarbonImmutable::now()->startOfDay();

        for ($offset = 0; $offset < 366; $offset++) {
            $date = $today->addDays($offset);

            if (! $this->scheduleFiresOn($days, $date)) {
                continue;
            }

            // Se hoje é dia de rodar mas já rodou, a próxima é a partir de amanhã.
            if ($offset === 0 && $this->portfolio->scheduledRuns()->whereDate('ran_on', $date->toDateString())->exists()) {
                continue;
            }

            return $date;
        }

        return null;
    }

    /**
     * @param  list<int>  $days
     */
    private function scheduleFiresOn(array $days, CarbonImmutable $date): bool
    {
        $daysInMonth = $date->daysInMonth;

        foreach ($days as $configured) {
            if (min($configured, $daysInMonth) === $date->day) {
                return true;
            }
        }

        return false;
    }

    public function addCompany(): void
    {
        $this->authorize('update', $this->portfolio);
        $this->validateOnly('cnpj');

        $cnpj = Cnpj::tryFrom($this->cnpj);

        if ($cnpj === null) {
            $this->addError('cnpj', 'CNPJ inválido.');

            return;
        }

        if ($this->portfolio->monitoredCompanies()->where('cnpj', $cnpj->value)->exists()) {
            $this->addError('cnpj', 'Este CNPJ já está no portfólio.');

            return;
        }

        $this->portfolio->monitoredCompanies()->create([
            'cnpj' => $cnpj->value,
            'label' => ($this->label !== null && $this->label !== '') ? $this->label : null,
        ]);

        $this->reset('cnpj', 'label');
        session()->flash('status', 'Empresa adicionada ao monitoramento.');
    }

    public function import(CompanyImporter $importer): void
    {
        $this->authorize('update', $this->portfolio);
        $this->validateOnly('csv');

        $path = $this->csv?->getRealPath();

        if ($path === null) {
            return;
        }

        $report = $importer->importFromFile($this->portfolio, $path);

        $this->importReport = [
            'imported' => $report->importedCount(),
            'rejected' => $report->rejected,
        ];

        $this->reset('csv');
        session()->flash('status', "Importação concluída: {$report->importedCount()} adicionada(s), {$report->rejectedCount()} rejeitada(s).");
    }

    public function queueRefresh(int $companyId): void
    {
        $company = $this->portfolio->monitoredCompanies()->findOrFail($companyId);
        $this->authorize('update', $company);

        $company->forceFill(['last_refresh_status' => null, 'last_refresh_error' => null])->save();
        RefreshMonitoredCompanyJob::dispatch($company);

        session()->flash('status', 'Atualização enfileirada.');
    }

    public function queueRefreshAll(QueuePortfolioRefresh $refresher): void
    {
        $this->authorize('update', $this->portfolio);

        $count = $refresher->handle($this->portfolio);

        session()->flash('status', "Atualização enfileirada para {$count} empresa(s).");
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = array_key_exists($status, self::FILTERS) ? $status : 'all';
        $this->resetPage();
    }

    /**
     * Reprocessa (revalida) apenas o subconjunto do filtro ativo — ex.: só os
     * CNPJs com erro, ou só os não encontrados.
     */
    public function reprocessFiltered(QueuePortfolioRefresh $refresher): void
    {
        $this->authorize('update', $this->portfolio);

        $count = $refresher->handle($this->portfolio, $this->statusConstraint($this->statusFilter));

        $label = mb_strtolower(self::FILTERS[$this->statusFilter] ?? 'todos');
        session()->flash('status', "{$count} empresa(s) ({$label}) reenfileirada(s) para validação.");
    }

    /**
     * Constraint de query correspondente ao filtro de status (null = sem filtro).
     *
     * @return (callable(Builder<MonitoredCompany>): void)|null
     */
    private function statusConstraint(string $filter): ?callable
    {
        return match ($filter) {
            'pending' => function (Builder $query): void {
                $query->whereNull('last_refresh_status');
            },
            'ok', 'nao_encontrado', 'erro' => function (Builder $query) use ($filter): void {
                $query->where('last_refresh_status', $filter);
            },
            default => null,
        };
    }

    public function removeCompany(int $companyId): void
    {
        $company = $this->portfolio->monitoredCompanies()->findOrFail($companyId);
        $this->authorize('delete', $company);

        $company->delete();
        session()->flash('status', 'Empresa removida do monitoramento.');
    }

    public function render(): View
    {
        $companiesQuery = $this->baseQuery()
            ->with('latestSnapshot')
            ->orderByDesc('id');

        $constraint = $this->statusConstraint($this->statusFilter);

        if ($constraint !== null) {
            $constraint($companiesQuery);
        }

        $companies = $companiesQuery->paginate(20);

        $counts = $this->baseQuery()
            ->toBase()
            ->selectRaw('last_refresh_status, COUNT(*) as aggregate')
            ->groupBy('last_refresh_status')
            ->pluck('aggregate', 'last_refresh_status');

        $stats = [
            'total' => (int) $counts->sum(),
            'ok' => (int) ($counts[RefreshStatus::Ok->value] ?? 0),
            'not_found' => (int) ($counts[RefreshStatus::NotFound->value] ?? 0),
            'errors' => (int) ($counts[RefreshStatus::Error->value] ?? 0),
            'pending' => (int) ($counts[''] ?? 0),
            'queued' => (int) DB::table('jobs')->count(),
        ];

        $filters = [
            'all' => ['label' => self::FILTERS['all'], 'count' => $stats['total']],
            'pending' => ['label' => self::FILTERS['pending'], 'count' => $stats['pending']],
            'ok' => ['label' => self::FILTERS['ok'], 'count' => $stats['ok']],
            'nao_encontrado' => ['label' => self::FILTERS['nao_encontrado'], 'count' => $stats['not_found']],
            'erro' => ['label' => self::FILTERS['erro'], 'count' => $stats['errors']],
        ];

        $savedDays = $this->normalizedScheduleDays($this->portfolio->schedule_days ?? []);

        return view('livewire.portfolios.show', [
            'companies' => $companies,
            'stats' => $stats,
            'filters' => $filters,
            'scheduledRuns' => $this->portfolio->scheduledRuns()->limit(12)->get(),
            'scheduleDirty' => $this->normalizedScheduleDays($this->scheduleDays) !== $savedDays,
            'nextRun' => $this->nextScheduledRun($savedDays),
        ]);
    }

    /**
     * @return Builder<MonitoredCompany>
     */
    private function baseQuery(): Builder
    {
        return MonitoredCompany::query()->where('portfolio_id', $this->portfolio->id);
    }
}
