<?php

declare(strict_types=1);

namespace App\Livewire\Empresas;

use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Services\Bens\BensRecebiveis as BensRecebiveisService;
use App\Support\Cnpj;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Card "Bens e recebíveis" (recuperação de crédito): aeronaves, participações
 * societárias e contratos públicos vigentes da empresa — onde procurar penhora.
 * Lazy (contratos vêm de API externa). No perfil público e na carteira; só dado
 * público.
 */
class BensRecebiveis extends Component
{
    use InteractsWithCurrentOrganization;

    #[Locked]
    public string $cnpj = '';

    public function mount(string $cnpj): void
    {
        $this->currentOrganizationId();

        $valido = Cnpj::tryFrom($cnpj);
        abort_if($valido === null, 404);

        $this->cnpj = $valido->value;
    }

    public function placeholder(): View
    {
        return view('livewire.empresas.bens-recebiveis-placeholder');
    }

    public function render(BensRecebiveisService $bens): View
    {
        return view('livewire.empresas.bens-recebiveis', ['bens' => $bens->daEmpresa($this->cnpj)]);
    }
}
