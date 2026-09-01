<?php

declare(strict_types=1);

use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;

/**
 * @return array{0: Organization, 1: User}
 */
function orgWithUser(): array
{
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    return [$org, $user];
}

it('scopes portfolio queries to the authenticated organization', function () {
    [$orgA, $userA] = orgWithUser();
    [$orgB] = orgWithUser();

    $portfolioA = Portfolio::factory()->for($orgA)->create();
    $portfolioB = Portfolio::factory()->for($orgB)->create();

    $this->actingAs($userA);

    $visibleIds = Portfolio::pluck('id');

    expect($visibleIds)->toContain($portfolioA->id)
        ->and($visibleIds)->not->toContain($portfolioB->id)
        ->and(Portfolio::find($portfolioB->id))->toBeNull()
        ->and(Portfolio::find($portfolioA->id))->not->toBeNull();
});

it('does not scope queries when no user is authenticated (system context)', function () {
    Portfolio::factory()->count(2)->create();

    // Jobs and console commands run unauthenticated and must see every tenant.
    expect(Portfolio::count())->toBe(2);
});

it('enforces portfolio ownership through the policy', function () {
    [$orgA, $userA] = orgWithUser();
    [$orgB] = orgWithUser();

    $portfolioA = Portfolio::factory()->for($orgA)->create();
    $portfolioB = Portfolio::factory()->for($orgB)->create();

    expect($userA->can('view', $portfolioA))->toBeTrue()
        ->and($userA->can('update', $portfolioA))->toBeTrue()
        ->and($userA->can('view', $portfolioB))->toBeFalse()
        ->and($userA->can('update', $portfolioB))->toBeFalse()
        ->and($userA->can('delete', $portfolioB))->toBeFalse();
});

it('denies cross-tenant access to a monitored company via the policy', function () {
    [$orgA, $userA] = orgWithUser();
    [$orgB, $userB] = orgWithUser();

    $companyA = MonitoredCompany::factory()->for(Portfolio::factory()->for($orgA))->create();
    $companyB = MonitoredCompany::factory()->for(Portfolio::factory()->for($orgB))->create();

    // Companies are not globally scoped (reachable by id) ...
    expect(MonitoredCompany::find($companyB->id))->not->toBeNull();

    // ... so the policy is what enforces isolation.
    expect($userA->can('view', $companyA))->toBeTrue()
        ->and($userA->can('view', $companyB))->toBeFalse()
        ->and($userB->can('view', $companyB))->toBeTrue()
        ->and($userA->can('delete', $companyB))->toBeFalse();
});
