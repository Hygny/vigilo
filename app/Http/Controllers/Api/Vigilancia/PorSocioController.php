<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vigilancia;

use App\DTO\Vigilancia\Empresa;
use App\Http\Controllers\Controller;
use App\Services\Vigilancia\EmpresaLookup;
use App\Support\NomeSocio;
use App\Support\SituacaoCadastral;
use App\Support\VigilanciaResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * POST /api/v1/vigilancia/empresas/por-socio
 * Body: { "nomes": ["Fulano de Tal"], "situacao": "ATIVA", "limite": 50 }
 *
 * Empresas em que os nomes aparecem no QSA (match por nome completo
 * normalizado: sem acento, caixa alta, espaços colapsados). É POST para os
 * nomes NÃO irem para a URL/logs de acesso (só para a trilha de auditoria
 * interna, que é o ponto). `nomes` vazio → 200 com lista vazia (não é erro).
 * O consumidor faz a checagem fina (homônimo, possível parente).
 */
final class PorSocioController extends Controller
{
    public function __invoke(Request $request, EmpresaLookup $lookup): JsonResponse
    {
        $nomesInput = $request->input('nomes');
        $nomesNorm = [];

        if (is_array($nomesInput)) {
            foreach ($nomesInput as $nome) {
                if (! is_string($nome)) {
                    continue;
                }

                $norm = NomeSocio::norm($nome);

                if ($norm !== '') {
                    $nomesNorm[] = $norm;
                }
            }
        }

        // Teto de cardinalidade: um POST com milhares de nomes viraria um
        // whereIn gigante. Mantém só os primeiros N (além do rate limit).
        $maxNomes = max(1, (int) config('vigilancia.por_socio_nomes_max', 50));
        $nomesNorm = array_slice(array_values(array_unique($nomesNorm)), 0, $maxNomes);

        $situacaoCode = null;
        $situacao = $request->input('situacao');

        if (is_string($situacao) && trim($situacao) !== '') {
            $situacaoCode = SituacaoCadastral::code($situacao);

            if ($situacaoCode === null) {
                return VigilanciaResponse::error('situacao_invalida', 'Situação inválida (use ATIVA, BAIXADA, INAPTA, SUSPENSA ou NULA).', 422);
            }
        }

        $max = (int) config('vigilancia.por_socio_max', 100);
        $limiteInput = $request->input('limite', 50);
        $limite = max(1, min(is_numeric($limiteInput) ? (int) $limiteInput : 50, $max));

        if ($nomesNorm === []) {
            return VigilanciaResponse::collection([]);
        }

        try {
            $empresas = $lookup->porSocio($nomesNorm, $situacaoCode, $limite);
        } catch (QueryException) {
            return VigilanciaResponse::error('base_indisponivel', 'Base CNPJ indisponível no momento.', 503);
        }

        return VigilanciaResponse::collection(
            array_map(fn (Empresa $empresa): array => $empresa->toArray(), $empresas),
        );
    }
}
