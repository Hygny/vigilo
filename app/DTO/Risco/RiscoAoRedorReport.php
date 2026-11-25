<?php

declare(strict_types=1);

namespace App\DTO\Risco;

/**
 * "Risco ao redor": as empresas ligadas ao centro por relação CERTA e quantas
 * delas têm sinal de risco. `pgfnConsultada`/`sancoesConsultadas` false = a
 * fonte não pôde ser lida agora — o selo diz o que NÃO foi checado em vez de
 * sugerir que está limpo.
 */
final readonly class RiscoAoRedorReport
{
    /**
     * @param  list<EmpresaLigadaRisco>  $ligadas
     */
    public function __construct(
        public array $ligadas,
        public bool $pgfnConsultada,
        public bool $sancoesConsultadas,
        // Cópia local da CGU velha demais (agendador parado): sanção nova pode faltar.
        public bool $sancoesDesatualizadas = false,
    ) {}

    /**
     * "Nenhuma ligada com sinal" só vale como garantia (verde) quando todas as
     * fontes foram consultadas e a cópia da CGU está em dia.
     */
    public function semSinalConfiavel(): bool
    {
        return $this->comAlerta() === [] && $this->naoConsultadas() === [] && ! $this->sancoesDesatualizadas;
    }

    public function total(): int
    {
        return count($this->ligadas);
    }

    /** @return list<EmpresaLigadaRisco> as com algum sinal, mais graves primeiro */
    public function comAlerta(): array
    {
        $com = array_values(array_filter($this->ligadas, fn (EmpresaLigadaRisco $e): bool => $e->temAlerta()));

        usort($com, fn (EmpresaLigadaRisco $a, EmpresaLigadaRisco $b): int => $this->peso($b) <=> $this->peso($a) ?: strcmp($a->nome, $b->nome));

        return $com;
    }

    /**
     * Selo curto: "3 de 12 empresas ligadas com dívida ativa ou sanção". Null =
     * nenhuma ligada com sinal (ou nenhuma ligada).
     */
    public function selo(): ?string
    {
        $com = $this->comAlerta();

        if ($com === []) {
            return null;
        }

        $tipos = array_values(array_filter([
            $this->algum($com, fn (EmpresaLigadaRisco $e): bool => $e->listaSuja) ? 'Lista Suja' : null,
            $this->algum($com, fn (EmpresaLigadaRisco $e): bool => $e->sancaoVigente) ? 'sanção' : null,
            $this->algum($com, fn (EmpresaLigadaRisco $e): bool => $e->dividaAtiva) ? 'dívida ativa' : null,
            $this->algum($com, fn (EmpresaLigadaRisco $e): bool => $e->situacaoNegativa) ? 'situação negativa' : null,
        ]));

        $ultimo = array_pop($tipos);
        $lista = $tipos === [] ? (string) $ultimo : implode(', ', $tipos).' ou '.$ultimo;
        $n = count($com);

        return $n.' de '.$this->total().' empresa'.($this->total() === 1 ? '' : 's').' ligada'.($this->total() === 1 ? '' : 's').' com '.$lista;
    }

    /**
     * Fontes que não puderam ser checadas agora (para o aviso).
     *
     * @return list<string>
     */
    public function naoConsultadas(): array
    {
        return array_values(array_filter([
            $this->sancoesConsultadas ? null : 'sanções (CGU)',
            $this->pgfnConsultada ? null : 'dívida ativa (PGFN)',
        ]));
    }

    private function peso(EmpresaLigadaRisco $e): int
    {
        return ($e->listaSuja ? 8 : 0) + ($e->sancaoVigente ? 4 : 0) + ($e->dividaAtiva ? 2 : 0) + ($e->situacaoNegativa ? 1 : 0);
    }

    /**
     * @param  list<EmpresaLigadaRisco>  $lista
     * @param  callable(EmpresaLigadaRisco): bool  $teste
     */
    private function algum(array $lista, callable $teste): bool
    {
        foreach ($lista as $e) {
            if ($teste($e)) {
                return true;
            }
        }

        return false;
    }
}
