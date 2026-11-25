<?php

declare(strict_types=1);

namespace App\Livewire\Empresas;

use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Services\Bens\IndiciosEsvaziamento as IndiciosService;
use App\Support\Cnpj;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Card "Indícios de esvaziamento e sucessão" (recuperação de crédito). O marco é
 * a 1ª inscrição em dívida ativa da União; o usuário pode trocar pela data da
 * dívida dele (título vencido, contrato inadimplido). Lazy: varre a base CNPJ.
 * No perfil público e na carteira; só dado público.
 */
class IndiciosEsvaziamento extends Component
{
    use InteractsWithCurrentOrganization;

    /** Datas aplicadas por usuário por minuto (cada uma dispara uma varredura). */
    private const DATAS_POR_MINUTO = 10;

    #[Locked]
    public string $cnpj = '';

    /** Marco informado e já validado (só muda por aplicarData/usarPgfn). */
    #[Locked]
    public ?string $marcoInformado = null;

    /** Campo do formulário (AAAA-MM-DD). */
    public string $data = '';

    public function mount(string $cnpj): void
    {
        $this->currentOrganizationId();

        $valido = Cnpj::tryFrom($cnpj);
        abort_if($valido === null, 404);

        $this->cnpj = $valido->value;
    }

    public function aplicarData(): void
    {
        $this->validate(
            ['data' => ['required', 'date_format:Y-m-d', 'after_or_equal:1990-01-01', 'before_or_equal:today']],
            attributes: ['data' => 'data da dívida'],
        );

        // Cada data nova é uma varredura sem cache na base CNPJ.
        $chave = 'esvaziamento-data:'.auth()->id();

        if (RateLimiter::tooManyAttempts($chave, self::DATAS_POR_MINUTO)) {
            $this->addError('data', 'Muitas datas seguidas. Aguarde um minuto.');

            return;
        }

        RateLimiter::hit($chave, 60);
        $this->marcoInformado = $this->data;
    }

    public function usarPgfn(): void
    {
        $this->reset('data', 'marcoInformado');
        $this->resetValidation();
    }

    public function placeholder(): View
    {
        return view('livewire.empresas.indicios-esvaziamento-placeholder');
    }

    public function render(IndiciosService $indicios): View
    {
        return view('livewire.empresas.indicios-esvaziamento', ['r' => $indicios->para($this->cnpj, $this->marcoInformado)]);
    }
}
