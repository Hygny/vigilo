<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait InteractsWithCurrentOrganization
{
    protected function currentUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        return $user;
    }

    protected function currentOrganizationId(): int
    {
        $organizationId = $this->currentUser()->organization_id;

        if ($organizationId === null) {
            abort(403, 'Usuário sem organização.');
        }

        return $organizationId;
    }
}
