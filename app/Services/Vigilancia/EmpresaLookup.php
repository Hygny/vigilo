<?php

declare(strict_types=1);

namespace App\Services\Vigilancia;

use App\DTO\Vigilancia\Empresa;
use App\DTO\Vigilancia\Endereco;
use App\DTO\Vigilancia\Socio;
use App\Providers\Cnpj\LocalCnpjProvider;
use App\Support\SituacaoCadastral;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Lê a base CNPJ local (PostgreSQL, conexão `cnpj`) e monta o objeto
 * {@see Empresa} do contrato da API OSINT. É leitura pura, SEM escopo de
 * carteira — serve o workflow externo que consulta CNPJ/endereço/sócio
 * arbitrários. Separado do {@see LocalCnpjProvider}
 * (aquele serve o pipeline snapshot/diff e tem o contrato CompanyData a
 * preservar).
 *
 * CNPJ de 14 díg = cnpj_basico(8) + cnpj_ordem(4) + cnpj_dv(2).
 */
final class EmpresaLookup
{
    public function __construct(private readonly string $connection) {}

    /**
     * Uma empresa pelo CNPJ (estabelecimento específico). Null = fora da base.
     */
    public function porCnpj(string $cnpj): ?Empresa
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== 14) {
            return null;
        }

        $basico = substr($digits, 0, 8);
        $ordem = substr($digits, 8, 4);
        $dv = substr($digits, 12, 2);

        $db = DB::connection($this->connection);

        $row = $db->table('estabelecimentos as e')
            ->leftJoin('empresas as emp', 'e.cnpj_basico', '=', 'emp.cnpj_basico')
            ->leftJoin('municipios as m', 'e.municipio', '=', 'm.codigo')
            ->leftJoin('cnaes as c', 'e.cnae_fiscal_principal', '=', 'c.codigo')
            ->where('e.cnpj_basico', $basico)
            ->where('e.cnpj_ordem', $ordem)
            ->where('e.cnpj_dv', $dv)
            ->first([
                'e.*',
                'emp.razao_social',
                'm.descricao as municipio_nome',
                'c.descricao as cnae_descricao',
            ]);

        if ($row === null) {
            return null;
        }

        return $this->hydrate((array) $row, $db, $basico, $digits);
    }

    /**
     * @param  array<string, mixed>  $e
     */
    private function hydrate(array $e, ConnectionInterface $db, string $basico, string $cnpj): Empresa
    {
        return new Empresa(
            cnpj: $cnpj,
            razaoSocial: $this->str($e['razao_social'] ?? null),
            nomeFantasia: $this->str($e['nome_fantasia'] ?? null),
            situacao: SituacaoCadastral::label($e['situacao_cadastral'] ?? null),
            dataSituacao: $this->date($e['data_situacao_cadastral'] ?? null),
            dataAbertura: $this->date($e['data_inicio_atividade'] ?? null),
            cnaeCodigo: $this->str($e['cnae_fiscal_principal'] ?? null),
            cnaeDescricao: $this->str($e['cnae_descricao'] ?? null),
            endereco: new Endereco(
                tipoLogradouro: $this->str($e['tipo_logradouro'] ?? null) ?? '',
                logradouro: $this->str($e['logradouro'] ?? null) ?? '',
                numero: $this->str($e['numero'] ?? null) ?? '',
                complemento: $this->str($e['complemento'] ?? null) ?? '',
                bairro: $this->str($e['bairro'] ?? null) ?? '',
                municipio: $this->str($e['municipio_nome'] ?? null),
                uf: $this->str($e['uf'] ?? null),
                cep: $this->cep($e['cep'] ?? null),
            ),
            socios: $this->socios($db, $basico),
        );
    }

    /**
     * @return list<Socio>
     */
    private function socios(ConnectionInterface $db, string $basico): array
    {
        $rows = $db->table('socios as s')
            ->leftJoin('qualificacoes_socios as q', 's.qualificacao_do_socio', '=', 'q.codigo')
            ->where('s.cnpj_basico', $basico)
            ->orderBy('s.nome_socio') // ordem estável p/ o consumidor (a base não garante)
            ->get([
                's.nome_socio',
                's.cnpj_cpf_do_socio',
                's.data_entrada_sociedade',
                'q.descricao as qualificacao',
            ]);

        $socios = [];

        foreach ($rows as $row) {
            $s = (array) $row;

            $socios[] = new Socio(
                nome: $this->str($s['nome_socio'] ?? null),
                qualificacao: $this->str($s['qualificacao'] ?? null),
                dataEntrada: $this->date($s['data_entrada_sociedade'] ?? null),
                documentoMascarado: $this->str($s['cnpj_cpf_do_socio'] ?? null),
            );
        }

        return $socios;
    }

    private function cep(mixed $value): ?string
    {
        $digits = preg_replace('/\D/', '', is_scalar($value) ? (string) $value : '') ?? '';

        return $digits === '' ? null : $digits;
    }

    private function date(mixed $value): ?string
    {
        $raw = is_scalar($value) ? trim((string) $value) : '';

        if ($raw === '' || $raw === '0' || $raw === '0000-00-00') {
            return null;
        }

        try {
            return Carbon::parse($raw)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
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
