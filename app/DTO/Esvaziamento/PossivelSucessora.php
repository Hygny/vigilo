<?php

declare(strict_types=1);

namespace App\DTO\Esvaziamento;

/**
 * Empresa que surgiu (ou recebeu sócio da devedora) DEPOIS do marco da dívida e
 * compartilha sinais com a devedora — candidata a sucessora/grupo econômico.
 * Indício, não prova: a força vem só de quantos sinais coincidem.
 */
final readonly class PossivelSucessora
{
    public const ENDERECO = 'endereco';

    public const CNAE = 'cnae';

    public const SOCIO = 'socio';

    public const CONTATO = 'contato';

    /** Rótulo de cada sinal, na ordem de exibição. */
    public const ROTULOS = [
        self::ENDERECO => 'mesmo endereço',
        self::CNAE => 'mesmo ramo (CNAE)',
        self::SOCIO => 'sócio em comum',
        self::CONTATO => 'mesmo telefone/e-mail',
    ];

    /**
     * @param  list<string>  $sinais  constantes desta classe
     * @param  list<string>  $sociosEmComum  nomes (QSA público)
     * @param  ?string  $inicio  início de atividade (AAAA-MM-DD) — da filial no endereço, se foi por ela que casou
     * @param  ?string  $entradaAposMarco  entrada mais recente de sócio em comum depois do marco
     * @param  bool  $filialNoEndereco  casou por uma FILIAL no endereço da devedora (empresa antiga, unidade nova)
     */
    public function __construct(
        public string $cnpj,
        public string $nome,
        public ?string $situacao,
        public ?string $inicio,
        public array $sinais,
        public array $sociosEmComum,
        public bool $abertaAposMarco,
        public ?string $entradaAposMarco,
        public bool $filialNoEndereco = false,
    ) {}

    /** 3+ sinais = forte; 2 = médio (só chega aqui com sócio ou CNAE entre eles). */
    public function forte(): bool
    {
        return count($this->sinais) >= 3;
    }

    /** O que aconteceu mais recentemente depois do marco: abertura ou entrada de sócio. */
    public function quando(): ?string
    {
        $datas = array_filter([$this->abertaAposMarco ? $this->inicio : null, $this->entradaAposMarco]);

        return $datas === [] ? null : max($datas);
    }

    /** @return list<string> */
    public function rotulos(): array
    {
        return array_map(fn (string $s): string => self::ROTULOS[$s] ?? $s, $this->sinais);
    }
}
