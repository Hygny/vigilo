<?php

declare(strict_types=1);

namespace App\Livewire\Companies;

use App\Livewire\Graph\GraphPage;
use App\Models\MonitoredCompany;
use App\Support\RazaoSocial;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;

/**
 * Grafo societário de uma empresa da CARTEIRA (autorizado pela empresa
 * monitorada). O pivô navega a base pública da Receita a partir dela.
 */
#[Layout('layouts.app')]
class Graph extends GraphPage
{
    use AuthorizesRequests;

    public MonitoredCompany $company;

    public function mount(MonitoredCompany $company): void
    {
        $this->authorize('view', $company);
        $this->company = $company;
    }

    protected function rootCnpj(): string
    {
        return $this->company->cnpj;
    }

    protected function pageContext(): array
    {
        return [
            'title' => RazaoSocial::exibir($this->company->latestSnapshot?->razao_social) ?? $this->company->label ?? $this->company->formattedCnpj(),
            'backUrl' => route('companies.show', $this->company),
            'backLabel' => 'Voltar à empresa',
            'resetLabel' => 'Voltar à empresa monitorada',
        ];
    }
}
