<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe uma rota ao super-admin (dono da plataforma). Todo método público
 * de Livewire do painel /admin também re-checa isSuperAdmin() no mount().
 */
class EnsureUserIsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user instanceof User && $user->isSuperAdmin(), 403);

        return $next($request);
    }
}
