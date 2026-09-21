<?php

declare(strict_types=1);

namespace App\Providers\Cnpj;

use App\Contracts\CnpjDataProvider;
use App\DTO\CompanyData;
use App\DTO\PartnerData;
use App\Support\Cnpj;
use App\Support\SituacaoCadastral;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

/**
 * CNPJ data provider backed by the local PostgreSQL base (dump da Receita,
 * conexão `cnpj`) — sem API de terceiros, sem rate limit.
 *
 * Monta exatamente o mesmo {@see CompanyData} que a BrasilAPI produzia, para o
 * pipeline snapshot/diff/alerta não mudar: traduz os campos que a Receita guarda
 * como código (situação cadastral, porte) e resolve nome de município / natureza
 * / qualificação de sócio por join nas tabelas de referência.
 *
 * CNPJ de 14 díg = cnpj_basico(8) + cnpj_ordem(4) + cnpj_dv(2).
 */
final class LocalCnpjProvider implements CnpjDataProvider
{
    /** Código do porte → rótulo. 00 (não informado) vira null. */
    private const PORTE = [
        '01' => 'ME',
        '03' => 'EPP',
        '05' => 'DEMAIS',
    ];

    public function __construct(private readonly string $connection) {}

    public function fetch(string $cnpj): ?CompanyData
    {
        // Fail fast em entrada malformada — nunca confundido com "não encontrado".
        $normalized = Cnpj::fromString($cnpj)->value;

        $basico = substr($normalized, 0, 8);
        $ordem = substr($normalized, 8, 4);
        $dv = substr($normalized, 12, 2);

        $db = DB::connection($this->connection);

        $row = $db->table('estabelecimentos as e')
            ->leftJoin('empresas as emp', 'e.cnpj_basico', '=', 'emp.cnpj_basico')
            ->leftJoin('municipios as m', 'e.municipio', '=', 'm.codigo')
            ->leftJoin('naturezas_juridicas as nj', 'emp.natureza_juridica', '=', 'nj.codigo')
            ->where('e.cnpj_basico', $basico)
            ->where('e.cnpj_ordem', $ordem)
            ->where('e.cnpj_dv', $dv)
            ->first([
                'e.*',
                'emp.razao_social',
                'emp.porte',
                'emp.natureza_juridica',
                'm.descricao as municipio_nome',
                'nj.descricao as natureza_nome',
            ]);

        if ($row === null) {
            return null; // não encontrado — mesma semântica do 404 da BrasilAPI
        }

        $e = (array) $row;
        $partners = $this->partners($db, $basico);

        return new CompanyData(
            cnpj: $normalized,
            razaoSocial: $this->str($e['razao_social'] ?? null) ?? '',
            nomeFantasia: $this->str($e['nome_fantasia'] ?? null),
            situacaoCadastral: SituacaoCadastral::label($e['situacao_cadastral'] ?? null),
            situacaoData: $this->str($e['data_situacao_cadastral'] ?? null),
            cnaePrincipal: $this->str($e['cnae_fiscal_principal'] ?? null),
            porte: $this->porte($e['porte'] ?? null),
            naturezaJuridica: $this->str($e['natureza_nome'] ?? null) ?? $this->str($e['natureza_juridica'] ?? null),
            logradouro: $this->logradouro($e),
            municipio: $this->str($e['municipio_nome'] ?? null) ?? $this->str($e['municipio'] ?? null),
            uf: $this->str($e['uf'] ?? null),
            partners: $partners,
            raw: ['estabelecimento' => $e, 'socios' => array_map(
                fn (PartnerData $p): array => ['nome' => $p->nome, 'documento' => $p->documento, 'qualificacao' => $p->qualificacao],
                $partners,
            )],
        );
    }

    /**
     * Sócios da empresa (por cnpj_basico), com a qualificação já resolvida para
     * texto via join na tabela de referência.
     *
     * @return list<PartnerData>
     */
    private function partners(ConnectionInterface $db, string $basico): array
    {
        $rows = $db->table('socios as s')
            ->leftJoin('qualificacoes_socios as q', 's.qualificacao_do_socio', '=', 'q.codigo')
            ->where('s.cnpj_basico', $basico)
            ->get(['s.nome_socio', 's.cnpj_cpf_do_socio', 'q.descricao as qualificacao']);

        $partners = [];

        foreach ($rows as $row) {
            $s = (array) $row;

            $partners[] = new PartnerData(
                nome: $this->str($s['nome_socio'] ?? null) ?? '',
                documento: $this->str($s['cnpj_cpf_do_socio'] ?? null),
                qualificacao: $this->str($s['qualificacao'] ?? null),
            );
        }

        return $partners;
    }

    /**
     * @param  array<string, mixed>  $e
     */
    private function logradouro(array $e): ?string
    {
        $rua = trim(implode(' ', array_filter([
            $this->str($e['tipo_logradouro'] ?? null),
            $this->str($e['logradouro'] ?? null),
        ])));

        $numero = $this->str($e['numero'] ?? null);

        if ($rua === '') {
            return $numero;
        }

        return $numero === null ? $rua : "{$rua}, {$numero}";
    }

    private function porte(mixed $code): ?string
    {
        $code = trim((string) ($code ?? ''));

        return self::PORTE[$code] ?? null;
    }

    private function str(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
