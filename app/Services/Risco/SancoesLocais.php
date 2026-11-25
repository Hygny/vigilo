<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Risco\RiscoReport;
use App\DTO\Risco\Sancao;
use App\Enums\FonteSancao;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Sanções da CGU (CEIS/CNEP/CEPIM) pela cópia LOCAL (`cgu_sancoes`, carregada
 * todo dia pelo `vigilo:sancoes-cgu`). Consulta barata, em lote, de qualquer
 * CNPJ — para o perfil público e as empresas ligadas, sem uma chamada à API da
 * CGU por empresa. Casa pela RAIZ do CNPJ (a sanção é da pessoa jurídica,
 * matriz e filiais).
 *
 * Só responde "sem sanção" se as três fontes já tiverem sido carregadas alguma
 * vez; antes disso, {@see disponivel()} é false e quem consome não afirma nada.
 */
final class SancoesLocais
{
    /** As três fontes carregadas ao menos uma vez? */
    public function disponivel(): bool
    {
        return DB::table('cgu_cargas')->whereIn('fonte', array_map(fn (FonteSancao $f): string => $f->value, FonteSancao::cases()))->count()
            === count(FonteSancao::cases());
    }

    /** Data (AAAA-MM-DD) do arquivo mais antigo entre as fontes carregadas — "cópia de". */
    public function referencia(): ?string
    {
        $data = DB::table('cgu_cargas')->min('data_arquivo');

        return is_string($data) ? substr($data, 0, 10) : null;
    }

    /** A cópia está velha demais para confiar (agendador parado)? */
    public function desatualizada(): bool
    {
        $ref = $this->referencia();
        $dias = (int) config('risco.cgu_download.max_idade_dias', 7);

        return $ref !== null && $dias > 0 && Carbon::parse($ref)->lt(now()->subDays($dias)->startOfDay());
    }

    /**
     * Relatório no mesmo formato do da API ({@see RiscoService}) para a regra do
     * semáforo ser uma só. Cópia não carregada → indisponível.
     */
    public function relatorio(string $cnpj): RiscoReport
    {
        if (! $this->disponivel()) {
            return RiscoReport::indisponivel($cnpj);
        }

        $basico = substr(preg_replace('/\D/', '', $cnpj) ?? '', 0, 8);

        return RiscoReport::ok($cnpj, $this->porBasicos([$basico])[$basico] ?? []);
    }

    /**
     * Sanções por raiz de CNPJ, numa consulta só.
     *
     * @param  list<string>  $basicos
     * @return array<string, list<Sancao>>
     */
    public function porBasicos(array $basicos): array
    {
        $basicos = array_values(array_unique(array_filter($basicos, fn (string $b): bool => preg_match('/^\d{8}$/', $b) === 1)));

        if ($basicos === []) {
            return [];
        }

        $porBasico = [];

        foreach (array_chunk($basicos, 500) as $lote) {
            $rows = DB::table('cgu_sancoes')
                ->whereIn('cnpj_basico', $lote)
                ->orderBy('data_inicio', 'desc')
                ->get(['fonte', 'cnpj_basico', 'categoria', 'orgao', 'data_inicio', 'data_fim']);

            foreach ($rows as $row) {
                $fonte = FonteSancao::tryFrom((string) $row->fonte);

                if ($fonte === null) {
                    continue;
                }

                $porBasico[(string) $row->cnpj_basico][] = new Sancao(
                    fonte: $fonte,
                    tipo: $this->str($row->categoria),
                    orgao: $this->str($row->orgao),
                    dataInicio: $this->date($row->data_inicio),
                    dataFim: $this->date($row->data_fim),
                    fundamentacao: null,
                );
            }
        }

        return $porBasico;
    }

    private function str(mixed $value): ?string
    {
        $value = is_scalar($value) ? trim((string) $value) : '';

        return $value === '' ? null : $value;
    }

    private function date(mixed $value): ?string
    {
        $value = $this->str($value);

        return $value === null ? null : substr($value, 0, 10);
    }
}
