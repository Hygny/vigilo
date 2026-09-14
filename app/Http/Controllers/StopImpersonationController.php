<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Encerra uma personificação: restaura o super-admin original (guardado em
 * session('impersonator_id')) e volta ao painel /admin. Funciona para a sessão
 * personificada — quem dispara é o usuário personificado, pelo banner.
 */
class StopImpersonationController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if ($impersonatorId === null) {
            return redirect()->route('dashboard');
        }

        $impersonator = User::find((int) $impersonatorId);

        if ($impersonator === null) {
            Auth::logout();

            return redirect()->route('login');
        }

        Auth::login($impersonator);

        return redirect()->route('admin.organizations.index');
    }
}
