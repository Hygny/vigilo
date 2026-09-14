<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Corta o acesso de usuários cuja organização foi suspensa (ex.: inadimplência):
 * desloga e manda pro login com aviso. Cobre o caso de a org ser suspensa com a
 * sessão já ativa — o bloqueio no login é feito em LoginForm. Super-admin (sem
 * org) e requisições sem usuário passam direto.
 */
class EnsureOrganizationActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User && ! $user->isSuperAdmin() && $user->organization?->isSuspended()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('status', 'Sua organização está suspensa. Fale com o suporte.');
        }

        return $next($request);
    }
}
