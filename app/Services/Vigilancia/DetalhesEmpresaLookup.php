<?php

declare(strict_types=1);

namespace App\Services\Vigilancia;

use App\DTO\Vigilancia\DetalhesEmpresa;
use App\DTO\Vigilancia\RegimeSimples;
use App\Support\Cnpj;
use App\Support\DataReceita;
use App\Support\PorteEmpresa;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Natureza jurídica, porte, capital social e Simples/MEI de uma empresa (pela
 * raiz do CNPJ) na base CNPJ local. O Simples vem de outra tabela
 * (`dados_simples`) e é lido à parte: se ela falhar, o resto sai igual.
 */
final class DetalhesEmpresaLookup
{
    public function __construct(
        private readonly string $connection,
    ) {}

    /** Null = empresa fora da base. */
    public function para(string $cnpj): ?DetalhesEmpresa
    {
        $basico = substr(Cnpj::normalize($cnpj), 0, 8);
        $db = DB::connection($this->connection);

        $row = $db->table('empresas as emp')
            ->leftJoin('naturezas_juridicas as nj', 'emp.natureza_juridica', '=', 'nj.codigo')
            ->where('emp.cnpj_basico', $basico)
            ->first(['emp.natureza_juridica', 'emp.porte', 'emp.capital_social', 'nj.descricao as natureza_nome']);

        if ($row === null) {
            return null;
        }

        $e = (array) $row;
        [$simples, $mei] = $this->simples($basico);

        return new DetalhesEmpresa(
            naturezaCodigo: $this->str($e['natureza_juridica'] ?? null),
            naturezaDescricao: $this->str($e['natureza_nome'] ?? null),
            porte: PorteEmpresa::label($e['porte'] ?? null),
            capitalSocial: is_numeric($e['capital_social'] ?? null) ? (float) $e['capital_social'] : null,
            simples: $simples,
            mei: $mei,
        );
    }

    /**
     * @return array{0: ?RegimeSimples, 1: ?RegimeSimples} [simples, mei]; [null, null] se a tabela falhou
     */
    private function simples(string $basico): array
    {
        try {
            $row = DB::connection($this->connection)
                ->table('dados_simples')
                ->where('cnpj_basico', $basico)
                ->first(['opcao_pelo_simples', 'data_opcao_pelo_simples', 'data_exclusao_do_simples', 'opcao_pelo_mei', 'data_opcao_pelo_mei', 'data_exclusao_do_mei']);
        } catch (QueryException $e) {
            report($e);

            return [null, null];
        }

        if ($row === null) {
            return [new RegimeSimples(false), new RegimeSimples(false)]; // nunca optou
        }

        $s = (array) $row;

        return [
            new RegimeSimples(
                optante: strtoupper((string) $this->str($s['opcao_pelo_simples'] ?? null)) === 'S',
                desde: DataReceita::ymd($s['data_opcao_pelo_simples'] ?? null),
                excluidoEm: DataReceita::ymd($s['data_exclusao_do_simples'] ?? null),
            ),
            new RegimeSimples(
                optante: strtoupper((string) $this->str($s['opcao_pelo_mei'] ?? null)) === 'S',
                desde: DataReceita::ymd($s['data_opcao_pelo_mei'] ?? null),
                excluidoEm: DataReceita::ymd($s['data_exclusao_do_mei'] ?? null),
            ),
        ];
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
