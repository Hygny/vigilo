<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use App\Support\LastAdminGuard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/** @return Builder<User> */
function orgUsersQuery(Organization $org): Builder
{
    return User::query()->where('organization_id', $org->id);
}

it('flags demoting/removing the sole admin as orphaning', function () {
    $org = Organization::factory()->create();
    $admin = User::factory()->for($org)->admin()->create();

    DB::transaction(function () use ($org, $admin): void {
        expect(LastAdminGuard::wouldOrphan(orgUsersQuery($org), $admin, true))->toBeTrue();
    });
});

it('does not flag when another admin remains', function () {
    $org = Organization::factory()->create();
    $admin = User::factory()->for($org)->admin()->create();
    User::factory()->for($org)->admin()->create(); // segundo admin

    DB::transaction(function () use ($org, $admin): void {
        // Rebaixando um admin, mas resta outro → não órfã.
        expect(LastAdminGuard::wouldOrphan(orgUsersQuery($org), $admin, true))->toBeFalse();
    });
});

it('does not flag when the target is not losing admin (e.g., promotion)', function () {
    $org = Organization::factory()->create();
    $admin = User::factory()->for($org)->admin()->create();

    // $losingAdmin = false → nunca órfã (o alvo continua admin).
    expect(LastAdminGuard::wouldOrphan(orgUsersQuery($org), $admin, false))->toBeFalse();
});
