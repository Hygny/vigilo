<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Guarda "a organização nunca pode ficar sem administrador" — fonte ÚNICA
 * consumida por Admin\Users::setRole/deleteUser e Admin\Organizations\Show::setRole
 * (antes o predicado + a contagem com lock estavam copiados nos três). Reconta os
 * admins da org COM lock, então deve rodar dentro de uma `DB::transaction` para
 * serializar rebaixamentos/remoções concorrentes (TOCTOU).
 */
final class LastAdminGuard
{
    /**
     * Rebaixar/remover `$target` deixaria a organização sem administrador?
     *
     * @param  Builder<User>  $orgUsers  query dos usuários da organização
     * @param  bool  $losingAdmin  a ação faz a org perder este admin (rebaixar ou remover)
     */
    public static function wouldOrphan(Builder $orgUsers, User $target, bool $losingAdmin): bool
    {
        if (! $target->isAdmin() || ! $losingAdmin) {
            return false;
        }

        return (int) $orgUsers->where('role', Role::Admin->value)->lockForUpdate()->count() <= 1;
    }
}
