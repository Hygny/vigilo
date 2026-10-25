<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Risco\PgfnDebtSummary;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Dívida ativa da União (PGFN) de QUALQUER empresa, lida da base CNPJ (conexão
 * `cnpj`, tabela `pgfn_dividas` — a PGFN inteira, só devedores PJ, carregada por
 * trimestre na VM pelo deploy/cnpj-db/bin/pgfn.sh).
 *
 * A consulta é pela RAIZ do CNPJ (cnpj_basico): filial não é pessoa jurídica
 * distinta, então a dívida inscrita na matriz (ou em outra filial) é dívida da
 * empresa. Leitura indexada (índice coberto por cnpj_basico), sem HTTP.
 *
 * Base fora do ar = {@see QueryException}: quem chama decide como degradar —
 * "sem dívida" nunca é inferido de uma consulta que falhou.
 */
final class PgfnLookup
{
    /** Teto de raízes por consulta (o IN fica num tamanho razoável). */
    private const CHUNK = 1000;

    public function __construct(private readonly string $connection) {}

    /**
     * Resumo da dívida da empresa do CNPJ, ou null se não há inscrição.
     *
     * @throws QueryException base CNPJ indisponível
     */
    public function para(string $cnpj): ?PgfnDebtSummary
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== 14) {
            return null;
        }

        $basico = substr($digits, 0, 8);

        return $this->paraBasicos([$basico])[$basico] ?? null;
    }

    /**
     * Resumo por raiz de CNPJ em consultas agrupadas (sem N+1). Só as raízes com
     * dívida aparecem no resultado.
     *
     * @param  list<string>  $basicos  raízes de 8 dígitos
     * @return array<string, PgfnDebtSummary>
     *
     * @throws QueryException base CNPJ indisponível
     */
    public function paraBasicos(array $basicos): array
    {
        $basicos = array_values(array_unique(array_filter(
            $basicos,
            fn (string $b): bool => preg_match('/^\d{8}$/', $b) === 1,
        )));

        $resumos = [];

        foreach (array_chunk($basicos, self::CHUNK) as $chunk) {
            $rows = DB::connection($this->connection)
                ->table('pgfn_dividas')
                ->whereIn('cnpj_basico', $chunk)
                ->groupBy('cnpj_basico')
                ->get([
                    'cnpj_basico',
                    DB::raw('count(*) as inscricoes'),
                    DB::raw('sum(case when ajuizado then 1 else 0 end) as ajuizadas'),
                    DB::raw('sum(valor) as total'),
                ]);

            foreach ($rows as $row) {
                $r = (array) $row;
                $basico = is_scalar($r['cnpj_basico'] ?? null) ? (string) $r['cnpj_basico'] : '';

                $resumos[$basico] = new PgfnDebtSummary(
                    inscricoes: (int) $this->num($r['inscricoes'] ?? 0),
                    total: (float) $this->num($r['total'] ?? 0),
                    ajuizadas: (int) $this->num($r['ajuizadas'] ?? 0),
                );
            }
        }

        return $resumos;
    }

    /**
     * Trimestre da PGFN carregado na base (ex.: "2026_trimestre_03"), ou null se
     * nenhuma carga foi feita.
     *
     * @throws QueryException base CNPJ indisponível
     */
    public function referencia(): ?string
    {
        $trimestre = DB::connection($this->connection)
            ->table('pgfn_cargas')
            ->orderByDesc('carregado_em')
            ->value('trimestre');

        return is_string($trimestre) ? $trimestre : null;
    }

    private function num(mixed $value): float|int
    {
        return is_numeric($value) ? $value + 0 : 0;
    }
}
