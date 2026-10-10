<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ApiAccessLog;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Grava a trilha de auditoria (LGPD) de cada chamada à API OSINT: token,
 * usuário, método, rota, parâmetros e resultado. Roda ANTES da checagem de
 * ability, então um 403 (token sem permissão) também fica registrado.
 *
 * A trilha é gravada SEMPRE — inclusive quando o controller lança exceção
 * (status derivado: o da HttpException, ou 500). Um acesso a dado pessoal nunca
 * pode ficar sem registro. A escrita do log, por sua vez, nunca derruba a
 * resposta nem mascara a exceção original (falha de auditoria é só reportada).
 *
 * Os nomes enviados ao POST /empresas/por-socio ficam em `params.input` — é de
 * propósito: é o dado pessoal cujo acesso precisa ser auditável.
 */
final class OsintAuditLog
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
            $this->record($request, $response->getStatusCode());

            return $response;
        } catch (Throwable $e) {
            $this->record($request, $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500);

            throw $e;
        }
    }

    /**
     * Grava a entrada de auditoria. Isolada em try/catch: uma falha ao gravar o
     * log não deve converter uma resposta pronta em 500 nem engolir a exceção
     * original do request.
     */
    private function record(Request $request, int $status): void
    {
        try {
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
                'status' => $status,
                'ip' => $request->ip(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
