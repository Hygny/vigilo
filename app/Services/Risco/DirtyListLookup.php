<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\Models\DirtyListEmployer;
use Illuminate\Support\Facades\DB;

/**
 * Consulta a Lista Suja do trabalho escravo (tabela local, indexada por
 * documento). Leitura barata — não precisa de cache nem de HTTP.
 *
 * Casa pela RAIZ do CNPJ: o empregador listado é a pessoa jurídica, e a lista
 * traz às vezes o CNPJ de uma filial (fazenda, obra) — consultar pela matriz
 * tem de achar. O próprio CNPJ, se listado, vem primeiro.
 */
final class DirtyListLookup
{
    /** A empresa (CNPJ) consta na Lista Suja? Devolve a entrada, ou null. */
    public function consta(string $cnpj): ?DirtyListEmployer
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== 14) {
            return null;
        }

        return DirtyListEmployer::query()
            ->where('documento', 'like', substr($digits, 0, 8).'%')
            ->whereRaw('length(documento) = 14') // CNPJ (CPF de 11 díg não casa por prefixo)
            ->orderByRaw('case when documento = ? then 0 else 1 end', [$digits])
            ->first();
    }

    /**
     * Raízes de CNPJ que constam na lista, numa consulta só.
     *
     * @param  list<string>  $basicos
     * @return array<string, DirtyListEmployer> raiz => entrada
     */
    public function raizesConstantes(array $basicos): array
    {
        $basicos = array_values(array_unique(array_filter($basicos, fn (string $b): bool => preg_match('/^\d{8}$/', $b) === 1)));

        if ($basicos === []) {
            return [];
        }

        $out = [];

        $entradas = DirtyListEmployer::query()
            ->whereIn(DB::raw('substr(documento, 1, 8)'), $basicos)
            ->whereRaw('length(documento) = 14')
            ->get();

        foreach ($entradas as $entrada) {
            $out[substr((string) $entrada->documento, 0, 8)] ??= $entrada;
        }

        return $out;
    }
}
