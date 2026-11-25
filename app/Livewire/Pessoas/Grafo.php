<?php

declare(strict_types=1);

namespace App\Livewire\Pessoas;

use App\Livewire\Concerns\CountsProfileViews;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Livewire\Graph\GraphPage;
use App\Models\PessoaConsulta;
use App\Services\Vigilancia\BuscaPessoas;
use App\Support\ConsultaComTimeout;
use App\Support\PessoaChave;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Throwable;

/**
 * Grafo centrado numa PESSOA provável (nome + CPF mascarado), aberto pela página
 * da pessoa: ela no centro e todas as empresas dela; dá para pivotar para as
 * empresas e voltar para a pessoa. A URL leva só a chave opaca. Conta no mesmo
 * teto do perfil e fica na trilha de consultas de pessoa (LGPD).
 *
 * Estrangeiro (documento zerado) e pessoa sem documento não têm grafo: o
 * documento sozinho casaria gente demais.
 */
#[Layout('layouts.app')]
class Grafo extends GraphPage
{
    use CountsProfileViews, InteractsWithCurrentOrganization;

    #[Locked]
    public string $chave = '';

    #[Locked]
    public bool $limitado = false;

    #[Locked]
    public string $titulo = 'Pessoa';

    #[Locked]
    public string $documento = '';

    #[Locked]
    public string $nome = '';

    public function mount(string $chave, BuscaPessoas $busca): void
    {
        $this->currentOrganizationId(); // só usuário de organização (403 senão)
        abort_unless(PessoaChave::valida($chave), 404);

        $this->chave = strtolower($chave);
        $this->limitado = $this->profileViewLimitExceeded();

        if ($this->limitado) {
            return;
        }

        PessoaConsulta::registrar($this->currentUser(), PessoaConsulta::TIPO_GRAFO, $this->chave);

        try {
            $pessoa = $busca->pessoa($this->chave);
        } catch (Throwable $e) {
            ConsultaComTimeout::reportarSemDados($e);

            return; // sem pessoa → o grafo mostra "indisponível"
        }

        // Mesmo critério do link na página da pessoa.
        abort_if($pessoa === null || $pessoa->documento === null || $pessoa->estrangeiro, 404);

        $this->documento = $pessoa->documento;
        $this->nome = $pessoa->nome;
        $this->titulo = $pessoa->nome;
    }

    /** A raiz é a pessoa ({@see rootPerson()}); nenhum CNPJ de partida. */
    protected function rootCnpj(): string
    {
        return '';
    }

    protected function rootPerson(): ?array
    {
        return $this->documento !== '' ? ['documento' => $this->documento, 'nome' => $this->nome] : null;
    }

    protected function blocked(): bool
    {
        return $this->limitado || $this->documento === '';
    }

    protected function pageContext(): array
    {
        return [
            'title' => $this->titulo,
            'backUrl' => route('pessoas.perfil', $this->chave),
            'backLabel' => 'Voltar à pessoa',
            'resetLabel' => 'Voltar à pessoa',
        ];
    }
}
