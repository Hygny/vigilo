<?php

declare(strict_types=1);

namespace App\Livewire\Pessoas;

use App\Livewire\Concerns\CountsProfileViews;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Models\PessoaConsulta;
use App\Services\Vigilancia\BuscaPessoas;
use App\Support\ConsultaComTimeout;
use App\Support\PessoaChave;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use PDOException;

/**
 * Página de uma pessoa PROVÁVEL (nome + CPF mascarado): as empresas em que ela
 * aparece como sócia e os homônimos (mesmo nome, outro documento). A URL leva só
 * a chave opaca. Cada abertura conta no teto do perfil (anti-raspagem) e fica
 * registrada ({@see PessoaConsulta}).
 */
#[Layout('layouts.app')]
class Perfil extends Component
{
    use CountsProfileViews, InteractsWithCurrentOrganization;

    #[Locked]
    public string $chave = '';

    #[Locked]
    public bool $limitado = false;

    public function mount(string $chave): void
    {
        $this->currentOrganizationId();
        abort_unless(PessoaChave::valida($chave), 404);

        $this->chave = strtolower($chave);
        $this->limitado = $this->profileViewLimitExceeded();

        if (! $this->limitado) {
            PessoaConsulta::registrar($this->currentUser(), PessoaConsulta::TIPO_PERFIL, $this->chave);
        }
    }

    public function render(BuscaPessoas $busca): View
    {
        $pessoa = null;
        $aviso = null;

        if (! $this->limitado) {
            try {
                $pessoa = $busca->pessoa($this->chave);
            } catch (QueryException|PDOException $e) { // PDOException: base fora ao abrir a transação do timeout
                if (ConsultaComTimeout::estourou($e)) {
                    $aviso = 'A consulta demorou demais para responder. Tente de novo em instantes.';
                } else {
                    ConsultaComTimeout::reportarSemDados($e);
                    $aviso = 'Base CNPJ indisponível no momento. Tente novamente em instantes.';
                }
            }
        }

        return view('livewire.pessoas.perfil', [
            'pessoa' => $pessoa,
            'aviso' => $aviso,
            'maxHomonimos' => BuscaPessoas::MAX_HOMONIMOS,
        ]);
    }
}
