<?php

declare(strict_types=1);

namespace App\DTO\Risco;

/**
 * Uma empresa ligada ao centro (sócio PJ, participação ou grupo econômico pelo
 * sócio PJ — só ligações CERTAS) e os sinais de risco dela nas fontes locais.
 */
final readonly class EmpresaLigadaRisco
{
    public function __construct(
        public string $nome,
        public string $cnpj,
        public string $ligacao,
        public ?string $situacao,
        public bool $situacaoNegativa,
        public bool $dividaAtiva,
        public bool $sancaoVigente,
        public bool $listaSuja,
    ) {}

    /** Códigos de sinal GRAVES (alerta de severidade alta). */
    public const CODIGOS_GRAVES = ['lista_suja', 'sancao'];

    /**
     * Códigos estáveis dos sinais (lista_suja, sancao, divida, situacao) — o que
     * o monitor guarda para saber se um sinal é NOVO.
     *
     * @return list<string>
     */
    public function codigos(): array
    {
        return array_values(array_filter([
            $this->listaSuja ? 'lista_suja' : null,
            $this->sancaoVigente ? 'sancao' : null,
            $this->dividaAtiva ? 'divida' : null,
            $this->situacaoNegativa ? 'situacao' : null,
        ]));
    }

    public function temAlerta(): bool
    {
        return $this->situacaoNegativa || $this->dividaAtiva || $this->sancaoVigente || $this->listaSuja;
    }

    /** @return list<string> sinais em pt-BR, do mais grave ao mais leve */
    public function sinais(): array
    {
        return array_map(fn (string $c): string => $this->rotulo($c), $this->codigos());
    }

    /** Rótulo em pt-BR de um código de sinal (ver codigos()). */
    public function rotulo(string $codigo): string
    {
        return match ($codigo) {
            'lista_suja' => 'Lista Suja (trabalho escravo)',
            'sancao' => 'sanção vigente (CGU)',
            'divida' => 'dívida ativa (PGFN)',
            'situacao' => 'situação '.$this->situacao,
            default => $codigo,
        };
    }
}
