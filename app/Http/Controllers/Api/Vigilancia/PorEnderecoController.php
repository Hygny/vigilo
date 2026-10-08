<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vigilancia;

use App\DTO\Vigilancia\Empresa;
use App\Http\Controllers\Controller;
use App\Services\Vigilancia\EmpresaLookup;
use App\Support\SituacaoCadastral;
use App\Support\VigilanciaResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GET /api/v1/vigilancia/empresas/por-endereco?cep=&numero=&situacao=&limite=
 *
 * Empresas no mesmo CEP + número (com sócios), direto da base. Match por CEP
 * exato + número normalizado (sem "Nº", caixa alta); complemento é ignorado.
 * `situacao` opcional; `limite` máx. no config (100). Ordena por abertura desc.
 * CEP sem endereço ("00000000") devolve 200 com lista vazia.
 */
final class PorEnderecoController extends Controller
{
    public function __invoke(Request $request, EmpresaLookup $lookup): JsonResponse
    {
        $cepParam = $request->query('cep');
        $cep = is_string($cepParam) ? (preg_replace('/\D/', '', $cepParam) ?? '') : '';

        if (strlen($cep) !== 8) {
            return VigilanciaResponse::error('cep_invalido', 'CEP deve conter 8 dígitos.', 422);
        }

        $numeroParam = $request->query('numero');
        $numero = is_string($numeroParam) ? trim($numeroParam) : '';

        if ($numero === '') {
            return VigilanciaResponse::error('numero_obrigatorio', 'Informe o parâmetro numero.', 422);
        }

        $situacaoCode = null;
        $situacao = $request->query('situacao');

        if (is_string($situacao) && trim($situacao) !== '') {
            $situacaoCode = SituacaoCadastral::code($situacao);

            if ($situacaoCode === null) {
                return VigilanciaResponse::error('situacao_invalida', 'Situação inválida (use ATIVA, BAIXADA, INAPTA, SUSPENSA ou NULA).', 422);
            }
        }

        $max = (int) config('vigilancia.por_endereco_max', 100);
        $limite = max(1, min((int) $request->query('limite', 50), $max));

        try {
            $empresas = $lookup->porEndereco($cep, $numero, $situacaoCode, $limite);
        } catch (QueryException) {
            return VigilanciaResponse::error('base_indisponivel', 'Base CNPJ indisponível no momento.', 503);
        }

        return VigilanciaResponse::collection(
            array_map(fn (Empresa $empresa): array => $empresa->toArray(), $empresas),
        );
    }
}
