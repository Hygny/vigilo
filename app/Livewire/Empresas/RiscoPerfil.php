<?php

declare(strict_types=1);

namespace App\Livewire\Empresas;

use App\DTO\Risco\RiscoAoRedorReport;
use App\DTO\Risco\RiscoAssessment;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Services\Risco\RiscoAoRedor;
use App\Services\Risco\RiscoAssessor;
use App\Services\Risco\SancoesLocais;
use App\Support\Cnpj;
use App\Support\Tentar;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Card "Risco" do perfil público de QUALQUER CNPJ: semáforo pela mesma regra
 * da carteira ({@see RiscoAssessor::paraPublico}, sanções da cópia local da
 * CGU) + "risco ao redor" (empresas ligadas com sinal de risco). Montado com
 * `lazy` pelo perfil — não atrasa o 1º paint nem roda de novo quando o perfil
 * re-renderiza. A abertura já contou no teto do perfil (o pai).
 */
class RiscoPerfil extends Component
{
    use InteractsWithCurrentOrganization;

    #[Locked]
    public string $cnpj = '';

    /** Rótulo da situação cadastral (ATIVA, BAIXADA…) que o perfil já leu. */
    #[Locked]
    public ?string $situacao = null;

    public function mount(string $cnpj, ?string $situacao = null): void
    {
        $this->currentOrganizationId(); // só usuário de organização

        $valido = Cnpj::tryFrom($cnpj);
        abort_if($valido === null, 404);

        $this->cnpj = $valido->value;
        $this->situacao = $situacao !== null && $situacao !== '' ? $situacao : null;
    }

    public function placeholder(): View
    {
        return view('livewire.companies.risco-panel-placeholder');
    }

    public function render(RiscoAssessor $assessor, RiscoAoRedor $aoRedor, SancoesLocais $sancoes): View
    {
        $assessment = Tentar::ouNull(fn (): RiscoAssessment => $assessor->paraPublico($this->cnpj, $this->situacao));

        return view('livewire.empresas.risco-perfil', [
            'assessment' => $assessment,
            'aoRedor' => Tentar::ouNull(fn (): RiscoAoRedorReport => $aoRedor->para($this->cnpj)),
            'copiaLocal' => [
                'referencia' => Tentar::ouNull(fn (): ?string => $sancoes->referencia()),
                'desatualizada' => (bool) Tentar::ouNull(fn (): bool => $sancoes->desatualizada()),
            ],
        ]);
    }
}
