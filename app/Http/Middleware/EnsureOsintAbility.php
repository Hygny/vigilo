<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\VigilanciaResponse;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Libera a API OSINT apenas para tokens que carregam explicitamente a ability
 * `vigilancia:osint` (emitida por `vigilo:osint-token`).
 *
 * Checagem explícita de propósito: NÃO usa `tokenCan()`/`abilities` do Sanctum,
 * porque o token coringa `*` (que o `vigilo:api-token` comum cria) passaria no
 * wildcard. Aqui exigimos a string literal — um token escopado à carteira não
 * entra na base inteira por acidente.
 */
final class EnsureOsintAbility
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $token = $user instanceof User ? $user->currentAccessToken() : null;
        $abilities = $token instanceof PersonalAccessToken ? $token->abilities : null;

        if (! is_array($abilities) || ! in_array('vigilancia:osint', $abilities, true)) {
            return VigilanciaResponse::error(
                'sem_permissao',
                'Token sem permissão para a API de vigilância.',
                403,
            );
        }

        return $next($request);
    }
}
