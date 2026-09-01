<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Scopes\OrganizationScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Makes a model tenant-aware:
 *  - a global scope constrains every query to the current user's organization;
 *  - new records inherit the current user's organization_id automatically.
 *
 * The model must have an `organization_id` column.
 */
trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope(new OrganizationScope);

        static::creating(function (Model $model): void {
            $user = Auth::user();

            if ($model->getAttribute('organization_id') === null && $user instanceof User) {
                $model->setAttribute('organization_id', $user->organization_id);
            }
        });
    }
}
