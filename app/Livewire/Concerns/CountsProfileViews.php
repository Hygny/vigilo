<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Teto por usuário das telas públicas de empresa (perfil e grafo de qualquer
 * CNPJ): cada abertura conta em `cnpj.perfil.per_minute` — anti-raspagem. Um
 * teto só para as duas, para o grafo não virar atalho em volta do perfil.
 *
 * Requer {@see InteractsWithCurrentOrganization} (currentUser()).
 */
trait CountsProfileViews
{
    /** Conta a abertura; true = estourou o teto (não consultar a base). */
    protected function profileViewLimitExceeded(): bool
    {
        $max = (int) config('cnpj.perfil.per_minute', 60);

        if ($max <= 0) {
            return false;
        }

        $key = 'perfil-empresa:'.(string) $this->currentUser()->getKey();

        if (RateLimiter::tooManyAttempts($key, $max)) {
            return true;
        }

        RateLimiter::hit($key, 60);

        return false;
    }
}
