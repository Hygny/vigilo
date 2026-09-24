<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\Graph\BeneficialOwner;
use App\Http\Controllers\Controller;
use App\Models\MonitoredCompany;
use App\Models\User;
use App\Services\Graph\OwnershipGraphService;
use App\Support\Cnpj;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Grafo societário (Camada 1) de um CNPJ, para integrações autenticadas.
 *
 * Escopado à carteira do dono do token: só monta o grafo para um CNPJ que a
 * organização já monitora — o pivô parte da própria carteira, nunca de um CNPJ
 * arbitrário (evita virar ferramenta de raspagem). O grafo, aí sim, revela as
 * empresas conectadas por sócio em comum, mesmo fora da carteira.
 */
final class CnpjGraphController extends Controller
{
    public function __invoke(Request $request, string $cnpj, OwnershipGraphService $graph): JsonResponse
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== Cnpj::LENGTH) {
            return response()->json(['message' => 'CNPJ deve conter 14 dígitos.'], 422);
        }

        $user = $request->user();
        $organizationId = $user instanceof User ? $user->organization_id : null;

        $monitored = MonitoredCompany::query()
            ->where('cnpj', $digits)
            ->whereHas('portfolio', function (Builder $query) use ($organizationId): void {
                $query->withoutGlobalScopes()->where('organization_id', $organizationId);
            })
            ->exists();

        if (! $monitored) {
            return response()->json(['message' => 'CNPJ não monitorado pela organização.'], 404);
        }

        // Por padrão só ligações certeiras; ?provaveis=1 inclui as prováveis
        // (mesmo CPF mascarado, nome divergente), marcadas com "provavel": true.
        return response()->json([
            ...$graph->for($digits, $request->boolean('provaveis'))->toArray(),
            'beneficiarios' => array_map(
                fn (BeneficialOwner $owner): array => $owner->toArray(),
                $graph->beneficialOwners($digits),
            ),
        ]);
    }
}
