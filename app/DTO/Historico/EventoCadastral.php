<?php

declare(strict_types=1);

namespace App\DTO\Historico;

/**
 * Uma mudança cadastral de uma empresa na base da Receita, já formatada para
 * leitura: o que mudou (`rotulo`), de quê (`antes`) para quê (`depois`) e em
 * que dump apareceu. Evento de estabelecimento traz o CNPJ dele; evento da
 * empresa (razão social, capital, sócios...) não.
 */
final readonly class EventoCadastral
{
    public function __construct(
        public string $campo,
        public string $rotulo,
        public ?string $antes,
        public ?string $depois,
        public string $detectadoEm, // Y-m-d
        public ?string $referencia, // dump da Receita (AAAA-MM)
        public ?string $estabelecimento, // CNPJ (14 díg) do estabelecimento, ou null
    ) {}

    /**
     * @return array{campo: string, rotulo: string, antes: ?string, depois: ?string, detectado_em: string, referencia: ?string, estabelecimento: ?string}
     */
    public function toArray(): array
    {
        return [
            'campo' => $this->campo,
            'rotulo' => $this->rotulo,
            'antes' => $this->antes,
            'depois' => $this->depois,
            'detectado_em' => $this->detectadoEm,
            'referencia' => $this->referencia,
            'estabelecimento' => $this->estabelecimento,
        ];
    }
}
