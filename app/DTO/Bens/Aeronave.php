<?php

declare(strict_types=1);

namespace App\DTO\Bens;

/**
 * Uma aeronave do Registro Aeronáutico Brasileiro (ANAC) ligada à empresa ou
 * pessoa consultada — como proprietária e/ou operadora. `gravame` = lista de
 * registros da ANAC separados por " / " (ônus como alienação fiduciária e
 * arrendamento, e também os marcadores de situação abaixo).
 *
 * Situação: CANCELADA = data de cancelamento OU o marcador "MATRICULA
 * CANCELADA" no gravame (a ANAC às vezes não preenche a data); RESERVADA =
 * "RESERVADAS AS MARCAS" (marca reservada, aeronave não matriculada — não é bem
 * a penhorar). Só a ATIVA conta como bem atual. Fonte única dessas regras: o
 * lookup monta o SQL de ordenação/contagem a partir das constantes daqui.
 */
final readonly class Aeronave
{
    public const PAPEL_PROPRIETARIO = 'proprietario';

    public const PAPEL_OPERADOR = 'operador';

    /** Ordem de exibição dos papéis. */
    public const PAPEIS = [self::PAPEL_PROPRIETARIO, self::PAPEL_OPERADOR];

    public const TIPO_PJ = 'PJ';

    public const TIPO_PF = 'PF';

    /** Marcadores do gravame (como a ANAC grava: caixa alta, sem acento). */
    public const GRAVAME_CANCELADA = 'MATRICULA CANCELADA';

    public const GRAVAME_RESERVADA = 'RESERVADAS AS MARCAS';

    public const GRAVAME_NENHUM = 'NENHUM GRAVAME REGISTRADO';

    /**
     * @param  bool  $comDataCancelamento  DT_CANC preenchida (o marcador do gravame entra em cancelada())
     * @param  list<string>  $papeis  PAPEL_* na ordem de PAPEIS
     * @param  ?float  $percentual  % de propriedade (do vínculo de proprietário)
     */
    public function __construct(
        public string $marca,
        public ?string $modelo,
        public ?string $fabricante,
        public ?int $ano,
        public ?string $gravame,
        public bool $comDataCancelamento,
        public array $papeis,
        public ?float $percentual,
        public ?string $uf,
    ) {}

    public function cancelada(): bool
    {
        return $this->comDataCancelamento || $this->temMarcador(self::GRAVAME_CANCELADA);
    }

    /** Marca reservada: ainda não é aeronave matriculada. */
    public function reservada(): bool
    {
        return ! $this->cancelada() && $this->temMarcador(self::GRAVAME_RESERVADA);
    }

    public function ativa(): bool
    {
        return ! $this->cancelada() && ! $this->reservada();
    }

    public function proprietaria(): bool
    {
        return in_array(self::PAPEL_PROPRIETARIO, $this->papeis, true);
    }

    /** Ônus registrados (o gravame sem os marcadores de situação e de "nenhum"), ou null. */
    public function onus(): ?string
    {
        $itens = array_filter(
            array_map('trim', explode('/', (string) $this->gravame)),
            fn (string $g): bool => $g !== '' && ! in_array(mb_strtoupper($g), [self::GRAVAME_NENHUM, self::GRAVAME_CANCELADA, self::GRAVAME_RESERVADA], true),
        );

        return $itens === [] ? null : implode(' / ', $itens);
    }

    /** Penhorável à primeira vista: ativa, a empresa é proprietária e sem ônus. */
    public function livre(): bool
    {
        return $this->ativa() && $this->proprietaria() && $this->onus() === null;
    }

    /** "proprietária e operadora" (empresa) ou "proprietário(a) e operador(a)" (pessoa). */
    public function rotuloPapeis(bool $empresa): string
    {
        $rotulos = $empresa
            ? [self::PAPEL_PROPRIETARIO => 'proprietária', self::PAPEL_OPERADOR => 'operadora']
            : [self::PAPEL_PROPRIETARIO => 'proprietário(a)', self::PAPEL_OPERADOR => 'operador(a)'];

        return implode(' e ', array_map(fn (string $p): string => $rotulos[$p] ?? $p, $this->papeis));
    }

    public function descricao(): string
    {
        return trim(implode(' ', array_filter([$this->fabricante, $this->modelo, $this->ano !== null ? "({$this->ano})" : null]))) ?: 'Aeronave';
    }

    private function temMarcador(string $marcador): bool
    {
        return str_contains(mb_strtoupper((string) $this->gravame), $marcador);
    }
}
