<?php

declare(strict_types=1);

namespace App\Livewire\Empresas;

use App\DTO\Busca\ResultadosBusca;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Services\Vigilancia\BuscaEmpresas;
use App\Support\BuscaNome;
use App\Support\Cnpj;
use App\Support\SituacaoCadastral;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Busca de QUALQUER empresa do Brasil por nome (razão social / nome fantasia)
 * na base CNPJ. Digitar um CNPJ leva direto ao perfil; CPF é recusado (não há
 * busca de pessoa por CPF). Cada busca executada conta no teto por usuário
 * (`cnpj.busca.per_minute`) — a base é pública, mas raspagem em massa não é o
 * uso do produto.
 */
#[Layout('layouts.app')]
class Busca extends Component
{
    use InteractsWithCurrentOrganization;

    /** UFs aceitas no filtro. */
    public const UFS = ['AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MG', 'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN', 'RO', 'RR', 'RS', 'SC', 'SE', 'SP', 'TO', 'EX'];

    #[Url(as: 'q', except: '')]
    public string $consulta = '';

    #[Url(except: '')]
    public string $uf = '';

    #[Url(except: '')]
    public string $situacao = '';

    /** Texto do campo (só vira `consulta` ao enviar). */
    public string $termo = '';

    public function mount(): void
    {
        $this->currentOrganizationId(); // só usuário de organização (403 senão)
        $this->termo = $this->consulta;
    }

    public function buscar(): void
    {
        $termo = trim($this->termo);
        $cnpj = Cnpj::tryFrom($termo);

        if ($cnpj !== null) {
            $this->redirectRoute('empresas.perfil', ['cnpj' => $cnpj->value], navigate: true);

            return;
        }

        $this->consulta = $termo;
    }

    public function render(BuscaEmpresas $busca): View
    {
        $resultados = null;
        $aviso = null;
        $consulta = trim($this->consulta);
        $uf = in_array($this->uf, self::UFS, true) ? $this->uf : null;
        $situacao = $this->situacao !== '' ? SituacaoCadastral::code($this->situacao) : null;

        if ($consulta !== '') {
            if (BuscaNome::pareceCpf($consulta)) {
                $aviso = 'Busca por CPF não é permitida. Pesquise pelo nome da empresa ou digite o CNPJ.';
            } elseif (BuscaNome::termos($consulta) === []) {
                $aviso = 'Digite ao menos uma palavra com 3 letras ou mais.';
            } elseif ($this->limitado()) {
                $aviso = 'Muitas buscas em pouco tempo. Aguarde um minuto e tente de novo.';
            } else {
                [$resultados, $aviso] = $this->executar($busca, $consulta, $uf, $situacao);
            }
        }

        return view('livewire.empresas.busca', [
            'resultados' => $resultados,
            'aviso' => $aviso,
            'ufs' => self::UFS,
            'situacoes' => SituacaoCadastral::labels(),
        ]);
    }

    /**
     * @return array{0: ResultadosBusca|null, 1: string|null}
     */
    private function executar(BuscaEmpresas $busca, string $consulta, ?string $uf, ?string $situacao): array
    {
        try {
            return [$busca->buscar($consulta, $uf, $situacao, max(1, (int) config('cnpj.busca.limite', 50))), null];
        } catch (QueryException $e) {
            if ((string) $e->getCode() === BuscaEmpresas::SQLSTATE_TIMEOUT) {
                return [null, 'Busca ampla demais para responder a tempo. Acrescente mais uma palavra do nome ou filtre por UF.'];
            }

            report($e);

            return [null, 'Base CNPJ indisponível no momento. Tente novamente em instantes.'];
        }
    }

    /** Conta a busca no teto do usuário; true = estourou (não busca). */
    private function limitado(): bool
    {
        $max = (int) config('cnpj.busca.per_minute', 20);

        if ($max <= 0) {
            return false;
        }

        $key = 'busca-empresas:'.(string) $this->currentUser()->getKey();

        if (RateLimiter::tooManyAttempts($key, $max)) {
            return true;
        }

        RateLimiter::hit($key, 60);

        return false;
    }
}
