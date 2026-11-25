<?php

declare(strict_types=1);

namespace App\Livewire\Empresas;

use App\Livewire\Concerns\CountsProfileViews;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Livewire\Graph\GraphPage;
use App\Support\Cnpj;
use App\Support\RazaoSocial;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Throwable;

/**
 * Grafo societário de QUALQUER CNPJ da base (a partir do perfil público). Lê
 * só dado público da Receita; não toca na carteira. Abrir a tela conta no mesmo
 * teto por usuário do perfil (`cnpj.perfil.per_minute`) e os pivôs no teto do
 * grafo — anti-raspagem.
 */
#[Layout('layouts.app')]
class Grafo extends GraphPage
{
    use CountsProfileViews, InteractsWithCurrentOrganization;

    #[Locked]
    public string $cnpj = '';

    #[Locked]
    public bool $limitado = false;

    /** Nome exibido no cabeçalho — lido uma vez no mount, não a cada request. */
    #[Locked]
    public string $titulo = '';

    public function mount(string $cnpj): void
    {
        $this->currentOrganizationId(); // só usuário de organização (403 senão)

        $valido = Cnpj::tryFrom($cnpj);
        abort_if($valido === null, 404);

        $this->cnpj = $valido->value;
        $this->limitado = $this->profileViewLimitExceeded();
        $this->titulo = ($this->limitado ? null : $this->razaoSocial()) ?? $valido->formatted();
    }

    protected function rootCnpj(): string
    {
        return $this->cnpj;
    }

    protected function blocked(): bool
    {
        return $this->limitado;
    }

    protected function pageContext(): array
    {
        return [
            'title' => $this->titulo,
            'backUrl' => route('empresas.perfil', $this->cnpj),
            'backLabel' => 'Voltar ao perfil',
            'resetLabel' => 'Voltar à empresa',
        ];
    }

    private function razaoSocial(): ?string
    {
        try {
            $razao = DB::connection((string) config('cnpj.providers.local.connection', 'cnpj'))
                ->table('empresas')
                ->where('cnpj_basico', substr($this->cnpj, 0, 8))
                ->value('razao_social');
        } catch (Throwable) {
            return null; // o grafo em si avisa se a base caiu
        }

        return RazaoSocial::exibir(is_string($razao) ? $razao : null);
    }
}
