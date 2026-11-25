<?php

declare(strict_types=1);

namespace App\Livewire\Empresas;

use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Services\Contratos\ContratosPublicos as ContratosPublicosService;
use App\Support\Cnpj;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Card "Contratos públicos" (empresa como fornecedora do governo federal),
 * consultado na hora no Portal da Transparência — por isso montado `lazy` (não
 * trava a página). Usado no perfil público e na página da empresa da carteira.
 * Só dado público; quem abriu a página já passou pelo teto/autorização dela.
 */
class ContratosPublicos extends Component
{
    use InteractsWithCurrentOrganization;

    /** Quantos contratos o card lista (os mais recentes). */
    private const MOSTRAR = 20;

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
        return view('livewire.empresas.contratos-publicos-placeholder');
    }

    public function render(ContratosPublicosService $contratos): View
    {
        return view('livewire.empresas.contratos-publicos', [
            'report' => $contratos->para($this->cnpj),
            'mostrar' => self::MOSTRAR,
        ]);
    }
}
