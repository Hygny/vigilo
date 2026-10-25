<?php

declare(strict_types=1);

namespace App\Livewire\Empresas;

use App\Actions\AddCompanyToPortfolio;
use App\Actions\Exceptions\CompanyNotAdded;
use App\DTO\Historico\HistoricoCadastral;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Models\MonitoredCompany;
use App\Models\Portfolio;
use App\Services\Vigilancia\EmpresaLookup;
use App\Services\Vigilancia\HistoricoLookup;
use App\Support\Cnpj;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Perfil de QUALQUER CNPJ da base da Receita (destino da busca): dados
 * cadastrais, quadro societário (CPF mascarado), dívida ativa (PGFN) e
 * histórico cadastral. Se a empresa já é monitorada pela organização, leva
 * para a página dela; senão, permite pôr num portfólio.
 *
 * Cada abertura conta no teto por usuário (`cnpj.perfil.per_minute`) — anti-
 * raspagem, como a busca e o pivô do grafo.
 */
#[Layout('layouts.app')]
class Perfil extends Component
{
    use AuthorizesRequests, InteractsWithCurrentOrganization;

    #[Locked]
    public string $cnpj = '';

    #[Locked]
    public bool $limitado = false;

    public ?int $portfolioId = null;

    public function mount(string $cnpj): void
    {
        $this->currentOrganizationId(); // só usuário de organização (403 senão)

        $valido = Cnpj::tryFrom($cnpj);
        abort_if($valido === null, 404);

        $this->cnpj = $valido->value;
        $this->limitado = $this->estourouLimite();
    }

    public function adicionar(AddCompanyToPortfolio $adder): void
    {
        $portfolio = Portfolio::query()
            ->where('organization_id', $this->currentOrganizationId())
            ->find($this->portfolioId);

        if ($portfolio === null) {
            $this->addError('portfolioId', 'Escolha um portfólio.');

            return;
        }

        $this->authorize('update', $portfolio);

        try {
            $adder->handle($portfolio, Cnpj::fromString($this->cnpj));
        } catch (CompanyNotAdded $e) {
            $this->addError('portfolioId', $e->getMessage());

            return;
        }

        session()->flash('status', "Empresa adicionada ao portfólio {$portfolio->name}.");
    }

    public function render(EmpresaLookup $empresas, HistoricoLookup $historicos): View
    {
        $empresa = null;
        $historico = null;
        $indisponivel = false;

        if (! $this->limitado) {
            try {
                $empresa = $empresas->porCnpj($this->cnpj);
            } catch (QueryException $e) {
                report($e);
                $indisponivel = true;
            }

            if ($empresa !== null) {
                $historico = $this->historico($historicos);
            }
        }

        $orgId = $this->currentOrganizationId();

        return view('livewire.empresas.perfil', [
            'empresa' => $empresa,
            'historico' => $historico,
            'indisponivel' => $indisponivel,
            'formatado' => Cnpj::fromString($this->cnpj)->formatted(),
            'monitorada' => MonitoredCompany::query()
                ->where('cnpj', $this->cnpj)
                ->whereHas('portfolio', fn ($q) => $q->where('organization_id', $orgId))
                ->first(),
            'portfolios' => Portfolio::query()->where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function historico(HistoricoLookup $historicos): ?HistoricoCadastral
    {
        try {
            return $historicos->para($this->cnpj);
        } catch (QueryException $e) {
            report($e);

            return null; // o perfil sai sem o histórico
        }
    }

    /** Conta a abertura no teto do usuário; true = estourou. */
    private function estourouLimite(): bool
    {
        $max = (int) config('cnpj.perfil.per_minute', 60);

        if ($max <= 0) {
            return false;
        }

        $key = 'perfil-empresa:'.(string) $this->currentUser()->getKey();

        if (RateLimiter::tooManyAttempts($key, $max)) {
            return true;
        }

        RateLimiter::hit($key, 60);

        return false;
    }
}
