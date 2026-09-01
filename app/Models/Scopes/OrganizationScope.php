<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Restricts queries to the authenticated user's organization.
 *
 * When there is no authenticated user (console commands, queued jobs) the scope
 * is a no-op, so system processes can operate across every tenant.
 *
 * @implements Scope<Model>
 */
final class OrganizationScope implements Scope
{
    /**
     * @param  Builder<*>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if ($user instanceof User && $user->organization_id !== null) {
            $builder->where($model->getTable().'.organization_id', $user->organization_id);
        }
    }
}
