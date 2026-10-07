<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;

/**
 * Respostas JSON no formato do contrato da API OSINT de vigilância.
 * Sucesso: `{ "data": ..., "meta": { "base_referencia": "YYYY-MM" } }`.
 * Erro:    `{ "error": { "code": "...", "message": "..." } }`.
 */
final class VigilanciaResponse
{
    /**
     * Um recurso único.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $extraMeta
     */
    public static function item(array $data, array $extraMeta = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => array_merge(['base_referencia' => self::baseReferencia()], $extraMeta),
        ]);
    }

    /**
     * Uma lista de recursos (inclui `meta.total`).
     *
     * @param  list<array<string, mixed>>  $data
     * @param  array<string, mixed>  $extraMeta
     */
    public static function collection(array $data, array $extraMeta = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => array_merge([
                'total' => count($data),
                'base_referencia' => self::baseReferencia(),
            ], $extraMeta),
        ]);
    }

    public static function error(string $code, string $message, int $status): JsonResponse
    {
        return response()->json([
            'error' => ['code' => $code, 'message' => $message],
        ], $status);
    }

    private static function baseReferencia(): ?string
    {
        $value = config('vigilancia.base_referencia');

        return is_string($value) && $value !== '' ? $value : null;
    }
}
