<?php

declare(strict_types=1);

namespace App\DTO\Bens;

/**
 * Uma marca do INPI (dados abertos) cujo titular é a empresa consultada. Marca
 * é bem móvel (Lei 9.279/96, art. 5º) — o REGISTRO em vigor é penhorável; o
 * PEDIDO em andamento é expectativa de direito; extinta/arquivada não é bem.
 */
final readonly class Marca
{
    public const EM_VIGOR = 'em_vigor';

    public const PEDIDO = 'pedido';

    public const INATIVA = 'inativa';

    public function __construct(
        public string $numero,
        public ?string $nome,
        public ?string $apresentacao,
        public ?string $natureza,
        public ?string $situacao,
        public ?string $dataDeposito,
        public ?string $dataConcessao,
        public ?string $dataVigencia,
    ) {}

    /** EM_VIGOR | PEDIDO | INATIVA, pela descrição da situação do INPI. */
    public function estado(): string
    {
        return self::estadoDe($this->situacao);
    }

    /**
     * Pela descrição de situação do INPI (vocabulário conferido na cópia real,
     * ~130 descrições): primeiro o que encerra (arquivado, extinto, nulo,
     * cancelado, indeferido em definitivo, inviável, caducado), depois o que é
     * registro vigente (inclui prorrogação, liminar e registro contestado), o
     * resto é pedido em andamento (oposição, exame, recurso, exigência...).
     */
    public static function estadoDe(?string $situacao): string
    {
        $s = mb_strtolower(trim((string) $situacao));

        if ($s === '' || preg_match('/arquivad|extint|inexistente|\bnulo\b|anulad|cancelad|indeferido \((sem|mantido)|n[aã]o publicado|^para (extinguir|arquivar)|invi[aá]vel|com caducidade|pela caducidade/u', $s) === 1) {
            return self::INATIVA;
        }

        if (preg_match('/em vigor|^registro\b|prazo extraordin[aá]rio de prorroga/u', $s) === 1) {
            return self::EM_VIGOR;
        }

        return self::PEDIDO;
    }

    public function emVigor(): bool
    {
        return $this->estado() === self::EM_VIGOR;
    }

    public function descricao(): string
    {
        return trim(($this->nome ?? 'Marca sem elemento nominativo').' · nº '.$this->numero);
    }
}
