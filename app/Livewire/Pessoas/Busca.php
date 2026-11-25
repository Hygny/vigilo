<?php

declare(strict_types=1);

namespace App\Livewire\Pessoas;

use App\DTO\Pessoas\ResultadosPessoas;
use App\Livewire\Concerns\InteractsWithCurrentOrganization;
use App\Models\PessoaConsulta;
use App\Services\Vigilancia\BuscaPessoas;
use App\Support\BuscaNome;
use App\Support\ConsultaComTimeout;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use PDOException;

/**
 * Busca de PESSOAS (sócios) pelo nome. Dado de pessoa física: o termo NÃO vai
 * para a URL (nem para log de acesso), toda busca executada fica registrada
 * (quem, quando, o quê — {@see PessoaConsulta}) e conta no teto por usuário
 * (`cnpj.pessoas.per_minute`). Exige nome E sobrenome (2 palavras) — reduz
 * homônimo e varredura.
 */
#[Layout('layouts.app')]
class Busca extends Component
{
    use InteractsWithCurrentOrganization;

    /** Texto do campo. */
    public string $termo = '';

    /** Última busca executada (só muda ao enviar o formulário). */
    #[Locked]
    public string $consulta = '';

    public function mount(): void
    {
        $this->currentOrganizationId(); // só usuário de organização (403 senão)
    }

    public function buscar(): void
    {
        $this->consulta = mb_substr(trim($this->termo), 0, 120);
    }

    public function render(BuscaPessoas $busca): View
    {
        $resultados = null;
        $aviso = null;
        $consulta = $this->consulta;

        if ($consulta !== '') {
            if (BuscaNome::pareceCpf($consulta) || preg_match('/\d{3}/', $consulta) === 1) {
                $aviso = 'Busca por CPF não é permitida. Pesquise pelo nome completo da pessoa.';
            } elseif (count(BuscaNome::termos($consulta)) < 2) {
                $aviso = 'Digite nome e sobrenome (duas palavras ou mais).';
            } elseif ($this->limitado()) {
                $aviso = 'Muitas buscas em pouco tempo. Aguarde um minuto e tente de novo.';
            } else {
                [$resultados, $aviso] = $this->executar($busca, $consulta);
            }
        }

        return view('livewire.pessoas.busca', ['resultados' => $resultados, 'aviso' => $aviso]);
    }

    /**
     * @return array{0: ResultadosPessoas|null, 1: string|null}
     */
    private function executar(BuscaPessoas $busca, string $consulta): array
    {
        PessoaConsulta::registrar($this->currentUser(), PessoaConsulta::TIPO_BUSCA, $consulta);

        try {
            return [$busca->buscar($consulta, max(1, (int) config('cnpj.pessoas.limite', 50))), null];
        } catch (QueryException|PDOException $e) { // PDOException: base fora ao abrir a transação do timeout
            if (ConsultaComTimeout::estourou($e)) {
                return [null, 'Nome comum demais para responder a tempo. Acrescente mais um sobrenome.'];
            }

            ConsultaComTimeout::reportarSemDados($e);

            return [null, 'Base CNPJ indisponível no momento. Tente novamente em instantes.'];
        }
    }

    private function limitado(): bool
    {
        $max = (int) config('cnpj.pessoas.per_minute', 20);

        if ($max <= 0) {
            return false;
        }

        $key = 'busca-pessoas:'.(string) $this->currentUser()->getKey();

        if (RateLimiter::tooManyAttempts($key, $max)) {
            return true;
        }

        RateLimiter::hit($key, 60);

        return false;
    }
}
