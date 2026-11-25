<?php

declare(strict_types=1);

namespace App\Livewire\Empresas;

use App\Actions\AddCompanyToPortfolio;
use App\Actions\Exceptions\CompanyNotAdded;
use App\DTO\Historico\HistoricoCadastral;
use App\Livewire\Concerns\CountsProfileViews;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Models\MonitoredCompany;
use App\Models\Portfolio;
use App\Services\Vigilancia\DetalhesEmpresaLookup;
use App\Services\Vigilancia\EmpresaLookup;
use App\Services\Vigilancia\HistoricoLookup;
use App\Support\Cnpj;
use App\Support\Tentar;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Perfil de QUALQUER CNPJ da base da Receita (destino da busca): dados
 * cadastrais (com natureza, porte, capital e Simples/MEI), quadro societário
 * (CPF mascarado), beneficiários finais, filiais, participações, dívida ativa
 * (PGFN) e histórico cadastral. Cada bloco degrada sozinho se a base falhar. Se a empresa já é monitorada pela organização, leva
 * para a página dela; senão, permite pôr num portfólio.
 *
 * Cada abertura conta no teto por usuário (`cnpj.perfil.per_minute`) — anti-
 * raspagem, como a busca e o pivô do grafo.
 */
#[Layout('layouts.app')]
class Perfil extends Component
{
    use AuthorizesRequests, CountsProfileViews, InteractsWithCurrentOrganization;

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
        $this->limitado = $this->profileViewLimitExceeded();
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

        session()->flash('status', "Empresa adicionada ao portfólio {$portfolio->name} — a primeira checagem já está na fila.");
    }

    public function render(EmpresaLookup $empresas, HistoricoLookup $historicos, DetalhesEmpresaLookup $detalhes): View
    {
        $empresa = null;
        $historico = null;
        $indisponivel = false;
        $extras = [];

        if (! $this->limitado) {
            try {
                $empresa = $empresas->porCnpj($this->cnpj);
            } catch (QueryException $e) {
                report($e);
                $indisponivel = true;
            }

            if ($empresa !== null) {
                $historico = $this->historico($historicos);
                $extras = $this->extras($detalhes);
            }
        }

        $orgId = $this->currentOrganizationId();

        return view('livewire.empresas.perfil', $extras + [
            'detalhes' => null,
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

    /**
     * Natureza/porte/capital/Simples (2 consultas por chave primária). A parte
     * pesada — beneficiários, participações, estabelecimentos — é o componente
     * lazy {@see PerfilEstrutura}. Falha → null = "indisponível" no bloco.
     *
     * @return array<string, mixed>
     */
    private function extras(DetalhesEmpresaLookup $detalhes): array
    {
        return ['detalhes' => Tentar::ouNull(fn (): mixed => $detalhes->para($this->cnpj))];
    }
}
