<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Traduz o código de situação cadastral da Receita (base CNPJ) para o texto que
 * o resto do sistema usa. Fonte única — consumido pelo LocalCnpjProvider e pelo
 * grafo societário. Código desconhecido/vazio → '' (o baseline do CompanyDiffer
 * trata '' como não-negativo).
 */
final class SituacaoCadastral
{
    /**
     * Código da Receita → rótulo. Tudo exceto ATIVA é "negativo" — o conjunto
     * negativo é DERIVADO daqui ({@see negativeLabels()}), inclusive para o
     * grifo.js (recebe a lista do servidor via @js). Adicionar um código aqui já
     * propaga para a coloração/filtro do grafo — sem lista duplicada para manter.
     *
     * @var array<string, string>
     */
    private const MAP = [
        '01' => 'NULA',
        '02' => 'ATIVA',
        '03' => 'SUSPENSA',
        '04' => 'INAPTA',
        '08' => 'BAIXADA',
    ];

    public static function label(mixed $code): string
    {
        return self::MAP[trim((string) ($code ?? ''))] ?? '';
    }

    /**
     * Inverso de {@see label()}: do rótulo (ATIVA/BAIXADA/…) para o código da
     * Receita. Null quando o rótulo não existe — usado para filtrar por situação
     * na base (que guarda o código, não o texto).
     */
    public static function code(string $label): ?string
    {
        return array_flip(self::MAP)[mb_strtoupper(trim($label))] ?? null;
    }

    /**
     * Todos os rótulos conhecidos (ATIVA, BAIXADA...) — p/ filtros de tela.
     *
     * @return list<string>
     */
    public static function labels(): array
    {
        return array_values(self::MAP);
    }

    /**
     * Rótulos de situação "negativa" (tudo exceto ATIVA) — fonte única derivada
     * do MAP. Consumido pelo contador do grafo e exposto ao grifo.js.
     *
     * @return list<string>
     */
    public static function negativeLabels(): array
    {
        return array_values(array_filter(self::MAP, static fn (string $label): bool => $label !== 'ATIVA'));
    }

    /** O rótulo representa uma situação negativa (baixada/inapta/suspensa/nula)? */
    public static function isNegative(?string $label): bool
    {
        return $label !== null && in_array(mb_strtoupper(trim($label)), self::negativeLabels(), true);
    }
}
