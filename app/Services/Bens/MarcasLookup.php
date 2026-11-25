<?php

declare(strict_types=1);

namespace App\Services\Bens;

use App\DTO\Bens\Marca;
use App\Support\DataReceita;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Marcas do INPI de titular pessoa jurídica, pela raiz do CNPJ — cópia na base
 * CNPJ da VM (`inpi_marcas`, carga semanal pelo deploy/cnpj-db/bin/inpi-marcas.sh).
 * Vínculo certo (CNPJ do titular). Pessoa física não está na cópia (LGPD).
 */
final class MarcasLookup
{
    /** Marcas lidas por empresa (no teto, a lista pode estar cortada). */
    public const LIMITE = 3000;

    private const ORDEM = [Marca::EM_VIGOR => 0, Marca::PEDIDO => 1, Marca::INATIVA => 2];

    public function __construct(private readonly string $connection) {}

    /**
     * Data (AAAA-MM-DD) da carga em uso, ou null se nunca carregada.
     *
     * @throws QueryException base CNPJ indisponível
     */
    public function referencia(): ?string
    {
        $em = DB::connection($this->connection)->table('inpi_cargas')->max('carregado_em');

        return DataReceita::ymd($em);
    }

    /**
     * Marcas da empresa: em vigor primeiro, depois pedidos, depois as inativas.
     *
     * @return array{marcas: list<Marca>, completa: bool}
     *
     * @throws QueryException base CNPJ indisponível
     */
    public function daEmpresa(string $cnpj): array
    {
        $basico = substr(preg_replace('/\D/', '', $cnpj) ?? '', 0, 8);

        if (strlen($basico) !== 8) {
            return ['marcas' => [], 'completa' => true];
        }

        $rows = DB::connection($this->connection)->table('inpi_marcas')
            ->where('cnpj_basico', $basico)
            ->orderByDesc('data_deposito')
            ->limit(self::LIMITE)
            ->get(['numero_inpi', 'marca', 'apresentacao', 'natureza', 'situacao', 'data_deposito', 'data_concessao', 'data_vigencia']);

        $porNumero = [];

        foreach ($rows as $r) {
            $numero = (string) $r->numero_inpi;
            $porNumero[$numero] ??= new Marca( // co-titularidade (matriz e filial): uma vez só
                numero: $numero,
                nome: is_string($r->marca) && trim($r->marca) !== '' ? trim($r->marca) : null,
                apresentacao: is_string($r->apresentacao) ? $r->apresentacao : null,
                natureza: is_string($r->natureza) ? $r->natureza : null,
                situacao: is_string($r->situacao) ? $r->situacao : null,
                dataDeposito: DataReceita::ymd($r->data_deposito),
                dataConcessao: DataReceita::ymd($r->data_concessao),
                dataVigencia: DataReceita::ymd($r->data_vigencia),
            );
        }

        $marcas = array_values($porNumero);
        usort($marcas, fn (Marca $a, Marca $b): int => [self::ORDEM[$a->estado()], $b->dataVigencia ?? '', $b->dataDeposito ?? ''] <=> [self::ORDEM[$b->estado()], $a->dataVigencia ?? '', $a->dataDeposito ?? '']);

        return ['marcas' => $marcas, 'completa' => count($rows) < self::LIMITE];
    }
}
