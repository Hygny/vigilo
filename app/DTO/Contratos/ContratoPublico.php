<?php

declare(strict_types=1);

namespace App\DTO\Contratos;

use Illuminate\Support\Carbon;

/**
 * Um contrato do Poder Executivo Federal em que a empresa é a FORNECEDORA
 * (Portal da Transparência). Valores em reais; datas AAAA-MM-DD.
 */
final readonly class ContratoPublico
{
    public function __construct(
        public ?int $id,
        public ?string $numero,
        public ?string $objeto,
        public ?string $orgao,
        public ?string $orgaoSigla,
        public ?string $modalidade,
        public ?string $situacao,
        public ?string $dataAssinatura,
        public ?string $vigenciaInicio,
        public ?string $vigenciaFim,
        public ?float $valorInicial,
        public ?float $valorFinal,
        public string $cnpjFornecedor,
    ) {}

    /**
     * Vigente hoje: fim da vigência hoje ou adiante E situação que não indique
     * contrato encerrado antes da hora (rescindido, cancelado, anulado...).
     * Sem data de fim → não afirma.
     */
    public function vigente(): bool
    {
        return $this->vigenciaFim !== null
            && $this->vigenciaFim >= now()->toDateString()
            && preg_match('/rescin|cancel|anula|encerr|extint|suspens/i', (string) $this->situacao) !== 1;
    }

    public function valor(): ?float
    {
        return $this->valorFinal ?? $this->valorInicial;
    }

    /** Linha de detalhe: órgão · modalidade · vigência · nº (só o que houver). */
    public function detalhe(): string
    {
        $br = fn (?string $d): string => $d !== null ? Carbon::parse($d)->format('d/m/Y') : '—';

        return implode(' · ', array_filter([
            $this->orgaoSigla !== null && $this->orgao !== null ? $this->orgaoSigla.' — '.$this->orgao : $this->orgao,
            $this->modalidade,
            $this->vigenciaInicio !== null || $this->vigenciaFim !== null ? 'vigência '.$br($this->vigenciaInicio).' a '.$br($this->vigenciaFim) : null,
            $this->numero !== null ? 'nº '.$this->numero : null,
            $this->situacao !== null && $this->situacao !== 'Publicado' ? $this->situacao : null,
        ]));
    }

    /** Página do contrato no Portal da Transparência. */
    public function url(): ?string
    {
        return $this->id !== null ? 'https://portaldatransparencia.gov.br/contratos/'.$this->id : null;
    }
}
