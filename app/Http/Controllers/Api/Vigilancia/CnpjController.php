<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vigilancia;

use App\Http\Controllers\Controller;
use App\Services\Vigilancia\EmpresaLookup;
use App\Support\Cnpj;
use App\Support\VigilanciaResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/vigilancia/cnpjs/{cnpj}
 *
 * Dados cadastrais de um CNPJ direto da base da Receita (sem escopo de
 * carteira). 404 quando o CNPJ não está na base — o workflow do n8n trata
 * qualquer não-2xx como "sem dados" e segue.
 */
final class CnpjController extends Controller
{
    public function __invoke(string $cnpj, EmpresaLookup $lookup): JsonResponse
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== Cnpj::LENGTH) {
            return VigilanciaResponse::error('cnpj_invalido', 'CNPJ deve conter 14 dígitos.', 422);
        }

        try {
            $empresa = $lookup->porCnpj($digits);
        } catch (QueryException) {
            return VigilanciaResponse::error('base_indisponivel', 'Base CNPJ indisponível no momento.', 503);
        }

        if ($empresa === null) {
            return VigilanciaResponse::error('nao_encontrado', 'CNPJ fora da base.', 404);
        }

        return VigilanciaResponse::item($empresa->toArray());
    }
}
