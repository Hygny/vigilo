<?php

declare(strict_types=1);

namespace App\Livewire\Portfolios;

use App\Actions\QueuePortfolioRefresh;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Models\Portfolio;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    use AuthorizesRequests, InteractsWithCurrentOrganization;

    #[Validate('required|string|min:2|max:255')]
    public string $name = '';

    public ?int $editingId = null;

    #[Validate('required|string|min:2|max:255')]
    public string $editingName = '';

    public function create(): void
    {
        $this->authorize('create', Portfolio::class);
        $this->validateOnly('name');

        Portfolio::create(['name' => $this->name]);

        $this->reset('name');
        session()->flash('status', 'Portfólio criado.');
    }

    public function startEdit(int $id): void
    {
        $portfolio = Portfolio::findOrFail($id);
        $this->authorize('update', $portfolio);

        $this->editingId = $portfolio->id;
        $this->editingName = $portfolio->name;
    }

    public function saveEdit(): void
    {
        $portfolio = Portfolio::findOrFail($this->editingId);
        $this->authorize('update', $portfolio);
        $this->validateOnly('editingName');

        $portfolio->update(['name' => $this->editingName]);

        $this->reset('editingId', 'editingName');
        session()->flash('status', 'Portfólio atualizado.');
    }

    public function cancelEdit(): void
    {
        $this->reset('editingId', 'editingName');
    }

    public function delete(int $id): void
    {
        $portfolio = Portfolio::findOrFail($id);
        $this->authorize('delete', $portfolio);

        $portfolio->delete();
        session()->flash('status', 'Portfólio removido.');
    }

    public function queueRefresh(int $id, QueuePortfolioRefresh $refresher): void
    {
        $portfolio = Portfolio::findOrFail($id);
        $this->authorize('update', $portfolio);

        // Mesma ação usada dentro do portfólio: zera status p/ "pendente" e
        // enfileira — assim o progresso aparece ao abrir o portfólio.
        $count = $refresher->handle($portfolio);

        session()->flash('status', "Atualização enfileirada para {$count} empresa(s).");
    }

    public function render(): View
    {
        $this->currentOrganizationId();

        $portfolios = Portfolio::query()
            ->withCount('monitoredCompanies')
            ->latest()
            ->get();

        return view('livewire.portfolios.index', ['portfolios' => $portfolios]);
    }
}
