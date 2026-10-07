<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ApiAccessLog;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Grava a trilha de auditoria (LGPD) de cada chamada à API OSINT: token,
 * usuário, método, rota, parâmetros e resultado. Roda ANTES da checagem de
 * ability, então um 403 (token sem permissão) também fica registrado.
 *
 * Os nomes enviados ao POST /empresas/por-socio ficam em `params.input` — é de
 * propósito: é o dado pessoal cujo acesso precisa ser auditável.
 */
final class OsintAuditLog
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();
        $token = $user instanceof User ? $user->currentAccessToken() : null;

        ApiAccessLog::create([
            'token_id' => $token instanceof PersonalAccessToken ? $token->getKey() : null,
            'user_id' => $user instanceof User ? $user->getKey() : null,
            'method' => $request->method(),
            'path' => $request->path(),
            'params' => [
                'route' => $request->route()?->parameters() ?? [],
                'input' => $request->except(['password', '_token']),
            ],
            'status' => $response->getStatusCode(),
            'ip' => $request->ip(),
        ]);

        return $response;
    }
}
