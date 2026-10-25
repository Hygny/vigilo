<?php

declare(strict_types=1);

namespace App\DTO\Vigilancia;

use App\DTO\Risco\PgfnDebtSummary;

/**
 * Empresa no formato do contrato da API OSINT (/api/v1/vigilancia) — igual nos
 * três endpoints. `situacao` em caixa alta (ATIVA/BAIXADA/...); datas em
 * YYYY-MM-DD; CNPJ só com dígitos.
 *
 * `divida_ativa` (PGFN, pela raiz do CNPJ): objeto com zeros quando consultada e
 * sem inscrição; null quando a PGFN não pôde ser consultada (nunca "sem dívida"
 * por falha).
 */
final readonly class Empresa
{
    /**
     * @param  list<Socio>  $socios
     */
    public function __construct(
        public string $cnpj,
        public ?string $razaoSocial,
        public ?string $nomeFantasia,
        public string $situacao,
        public ?string $dataSituacao,
        public ?string $dataAbertura,
        public ?string $cnaeCodigo,
        public ?string $cnaeDescricao,
        public Endereco $endereco,
        public array $socios,
        public ?PgfnDebtSummary $dividaAtiva = null,
        public bool $dividaConsultada = false,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'cnpj' => $this->cnpj,
            'razao_social' => $this->razaoSocial,
            'nome_fantasia' => $this->nomeFantasia,
            'situacao' => $this->situacao,
            'data_situacao' => $this->dataSituacao,
            'data_abertura' => $this->dataAbertura,
            'cnae_principal' => [
                'codigo' => $this->cnaeCodigo,
                'descricao' => $this->cnaeDescricao,
            ],
            'endereco' => $this->endereco->toArray(),
            'socios' => array_map(fn (Socio $socio): array => $socio->toArray(), $this->socios),
            'divida_ativa' => $this->dividaConsultada
                ? ($this->dividaAtiva ?? new PgfnDebtSummary(0, 0.0))->toArray()
                : null,
        ];
    }
}
