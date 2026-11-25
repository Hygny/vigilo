<?php

declare(strict_types=1);

namespace App\Livewire\Empresas;

use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Services\Graph\OwnershipGraphService;
use App\Support\Cnpj;
use App\Support\Tentar;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Estrutura societária do perfil público: beneficiários finais (sobe a cadeia
 * pelos sócios PJ — a parte pesada), participações e estabelecimentos. Montado
 * `lazy` pelo perfil: não atrasa o 1º paint e não roda de novo quando o perfil
 * re-renderiza (ex.: ação "Adicionar à carteira"). A abertura já contou no teto
 * do perfil (o pai). Cada bloco degrada sozinho.
 */
class PerfilEstrutura extends Component
{
    use InteractsWithCurrentOrganization;

    #[Locked]
    public string $cnpj = '';

    public function mount(string $cnpj): void
    {
        $this->currentOrganizationId(); // só usuário de organização

        $valido = Cnpj::tryFrom($cnpj);
        abort_if($valido === null, 404);

        $this->cnpj = $valido->value;
    }

    public function placeholder(): View
    {
        return view('livewire.empresas.perfil-estrutura-placeholder');
    }

    public function render(OwnershipGraphService $grafo): View
    {
        $limite = max(1, (int) config('cnpj.perfil.participacoes_limite', 100));
        $participacoes = Tentar::ouNull(fn (): array => $grafo->participations($this->cnpj, $limite));

        return view('livewire.empresas.perfil-estrutura', [
            'cnpj' => $this->cnpj,
            'beneficiarios' => Tentar::ouNull(fn (): array => $grafo->beneficialOwners($this->cnpj)),
            'filiais' => Tentar::ouNull(fn (): array => $grafo->branches($this->cnpj)),
            'estabelecimentos' => (int) Tentar::ouNull(fn (): int => $grafo->establishmentCount($this->cnpj)),
            'participacoes' => $participacoes,
            // Só conta à parte se a lista encheu (senão o total é o tamanho dela).
            // Se só a contagem falhar, fica o tamanho da lista (nunca "sócia de 0").
            'totalParticipacoes' => is_array($participacoes) && count($participacoes) >= $limite
                ? (Tentar::ouNull(fn (): int => $grafo->participationCount($this->cnpj)) ?? count($participacoes))
                : (is_array($participacoes) ? count($participacoes) : 0),
        ]);
    }
}
