<?php

declare(strict_types=1);

namespace App\DTO\Esvaziamento;

use App\DTO\Graph\Participacao;

/**
 * Indícios de esvaziamento patrimonial e sucessão de uma empresa devedora — o
 * que mudou DEPOIS do marco da dívida (1ª inscrição em dívida ativa da União ou
 * data informada pelo usuário). Cada bloco pode ter falhado à parte: null = não
 * consultado agora (a tela nunca diz "nenhum indício" por falha).
 */
final readonly class EsvaziamentoReport
{
    public const MARCO_PGFN = 'pgfn';

    public const MARCO_INFORMADO = 'informado';

    public const NIVEL_ALTO = 'alto';

    public const NIVEL_MEDIO = 'medio';

    public const NIVEL_NENHUM = 'nenhum';

    /** Nada achado, mas algum bloco não respondeu. */
    public const NIVEL_PARCIAL = 'parcial';

    /** Participações novas exibidas no card (o total vem da lista). */
    public const PARTICIPACOES_EXIBIDAS = 10;

    /**
     * @param  ?string  $marco  AAAA-MM-DD; null = sem marco (sem dívida na PGFN e nada informado)
     * @param  ?string  $origemMarco  MARCO_PGFN | MARCO_INFORMADO
     * @param  bool  $pgfnConsultada  a PGFN respondeu (sem marco por falta de dívida ≠ por falha)
     * @param  ?string  $encerradaSituacao  BAIXADA/INAPTA/SUSPENSA se encerrou depois do marco
     * @param  list<PossivelSucessora>|null  $sucessoras  as primeiras (fortes primeiro)
     * @param  int  $outrasLigadas  empresas posteriores ao marco com sinal fraco demais para listar
     * @param  bool  $parcial  alguma leitura bateu no teto (endereço, pessoas, candidatas, histórico, participações): pode haver mais
     * @param  list<MovimentoSocio>|null  $entradas  sócios atuais que entraram depois do marco
     * @param  list<MovimentoSocio>|null  $saidas  saídas de sócio depois do marco (histórico)
     * @param  list<ReducaoCapital>|null  $reducoesCapital  (histórico)
     * @param  ?string  $historicoDesde  AAAA-MM do início do histórico cadastral
     * @param  list<Participacao>|null  $participacoes  empresas em que a devedora entrou como sócia depois do marco
     * @param  bool  $foraDaBase  a base CNPJ respondeu mas não tem a matriz (aberta depois da última atualização)
     */
    public function __construct(
        public ?string $marco,
        public ?string $origemMarco,
        public bool $pgfnConsultada,
        public ?float $dividaTotal = null,
        public int $inscricoes = 0,
        public ?string $encerradaSituacao = null,
        public ?string $encerradaEm = null,
        public ?array $sucessoras = null,
        public int $totalSucessoras = 0,
        public int $outrasLigadas = 0,
        public bool $parcial = false,
        public ?array $entradas = null,
        public bool $quadroTodoAposMarco = false,
        public ?array $saidas = null,
        public ?array $reducoesCapital = null,
        public ?string $historicoDesde = null,
        public ?array $participacoes = null,
        public bool $foraDaBase = false,
    ) {}

    public function semMarco(): bool
    {
        return $this->marco === null;
    }

    public function sucessorasFortes(): int
    {
        return count(array_filter($this->sucessoras ?? [], fn (PossivelSucessora $s): bool => $s->forte()));
    }

    /**
     * Indícios encontrados, em frases curtas (resumo do card).
     *
     * @return list<string>
     */
    public function indicios(): array
    {
        return array_values(array_filter([
            $this->totalSucessoras > 0 ? $this->totalSucessoras.' possível(is) sucessora(s)' : null,
            $this->encerradaSituacao !== null ? strtolower($this->encerradaSituacao).' depois da dívida' : null,
            $this->quadroTodoAposMarco ? 'quadro societário todo trocado' : (($this->entradas ?? []) !== [] ? count($this->entradas ?? []).' sócio(s) entrou(aram) depois' : null),
            ($this->saidas ?? []) !== [] ? count($this->saidas ?? []).' saída(s) de sócio' : null,
            ($this->reducoesCapital ?? []) !== [] ? 'redução de capital' : null,
            ($this->participacoes ?? []) !== [] ? count($this->participacoes ?? []).' participação(ões) nova(s)' : null,
        ]));
    }

    /**
     * NIVEL_*. Nada achado com algum bloco que não respondeu, ou com leitura
     * cortada no teto, = PARCIAL, nunca NENHUM ("nada encontrado" só com tudo lido).
     */
    public function nivel(): string
    {
        if ($this->sucessorasFortes() > 0 || ($this->encerradaSituacao !== null && ($this->totalSucessoras > 0 || $this->quadroTodoAposMarco))) {
            return self::NIVEL_ALTO;
        }

        if ($this->indicios() !== []) {
            return self::NIVEL_MEDIO;
        }

        return $this->completo() && ! $this->parcial ? self::NIVEL_NENHUM : self::NIVEL_PARCIAL;
    }

    /** Todos os blocos responderam (só então o relatório vai para o cache). */
    public function completo(): bool
    {
        return $this->marco !== null
            && $this->sucessoras !== null
            && $this->entradas !== null
            && $this->saidas !== null
            && $this->reducoesCapital !== null
            && $this->participacoes !== null;
    }
}
