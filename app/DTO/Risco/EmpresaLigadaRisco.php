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

    public function temAlerta(): bool
    {
        return $this->situacaoNegativa || $this->dividaAtiva || $this->sancaoVigente || $this->listaSuja;
    }

    /** @return list<string> sinais em pt-BR, do mais grave ao mais leve */
    public function sinais(): array
    {
        return array_values(array_filter([
            $this->listaSuja ? 'Lista Suja (trabalho escravo)' : null,
            $this->sancaoVigente ? 'sanção vigente (CGU)' : null,
            $this->dividaAtiva ? 'dívida ativa (PGFN)' : null,
            $this->situacaoNegativa ? 'situação '.$this->situacao : null,
        ]));
    }
}
