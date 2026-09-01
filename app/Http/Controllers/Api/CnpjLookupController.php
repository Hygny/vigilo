<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChangeEvent;
use App\Models\MonitoredCompany;
use App\Models\User;
use App\Support\Cnpj;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Serves this app's known cadastral data for a single CNPJ to authenticated
 * integrations (Bearer token via Sanctum).
 *
 * Scoped to the token owner's organization: only CNPJs that the organization
 * already monitors — and that have at least one collected snapshot — are
 * returned. Anything else is a 404, which the consumer treats as "pendente"
 * (não trava), per the agreed contract.
 */
final class CnpjLookupController extends Controller
{
    public function __invoke(Request $request, string $cnpj): JsonResponse
    {
        // Só normalização (14 dígitos), sem mod-11: quem consulta manda o CNPJ
        // que quer verificar, e os CNPJs guardados já foram validados na entrada.
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== Cnpj::LENGTH) {
            return response()->json(['message' => 'CNPJ deve conter 14 dígitos.'], 422);
        }

        $user = $request->user();
        $organizationId = $user instanceof User ? $user->organization_id : null;

        $company = MonitoredCompany::query()
            ->where('cnpj', $digits)
            ->whereHas('portfolio', function (Builder $query) use ($organizationId): void {
                $query->withoutGlobalScopes()->where('organization_id', $organizationId);
            })
            ->with('latestSnapshot')
            ->first();

        $snapshot = $company?->latestSnapshot;

        if ($company === null || $snapshot === null) {
            return response()->json(
                ['message' => 'CNPJ não monitorado ou ainda sem coleta.'],
                404,
            );
        }

        return response()->json([
            'cnpj' => $digits,
            'situacao_cadastral' => $snapshot->situacao_cadastral,
            'cnae_principal' => $snapshot->cnae_principal,
            'razao_social' => $snapshot->razao_social,
            'historico' => $company->changeEvents()
                ->orderByDesc('detected_at')
                ->orderByDesc('id')
                ->limit((int) config('cnpj.api.history_limit'))
                ->get()
                ->map(fn (ChangeEvent $event): array => [
                    'data' => $event->detected_at->toDateString(),
                    'campo' => $event->field,
                    'de' => $event->old_value,
                    'para' => $event->new_value,
                ])
                ->all(),
        ]);
    }
}
