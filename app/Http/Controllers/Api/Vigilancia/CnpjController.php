<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vigilancia;

use App\Http\Controllers\Controller;
use App\Services\Vigilancia\EmpresaLookup;
use App\Services\Vigilancia\HistoricoLookup;
use App\Support\Cnpj;
use App\Support\VigilanciaResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/v1/vigilancia/cnpjs/{cnpj}
 *
 * Dados cadastrais de um CNPJ direto da base da Receita (sem escopo de
 * carteira), com a dívida ativa (PGFN) e o histórico cadastral. 404 quando o
 * CNPJ não está na base — o workflow do n8n trata qualquer não-2xx como "sem
 * dados" e segue.
 *
 * `historico_cadastral` só existe neste endpoint (as listas por endereço/sócio
 * não carregam): `{desde, sinais_12m, eventos}`, ou null se o histórico não
 * pôde ser lido — a resposta cadastral sai mesmo assim.
 */
final class CnpjController extends Controller
{
    public function __invoke(string $cnpj, EmpresaLookup $lookup, HistoricoLookup $historico): JsonResponse
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

        try {
            $historicoCadastral = $historico->para($digits)?->toArray();
        } catch (QueryException $e) {
            report($e);
            $historicoCadastral = null;
        }

        return VigilanciaResponse::item($empresa->toArray() + ['historico_cadastral' => $historicoCadastral]);
    }
}
