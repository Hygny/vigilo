<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MonitoredCompany;
use App\Models\Portfolio;
use App\Models\User;

class MonitoredCompanyPolicy
{
    public function view(User $user, MonitoredCompany $company): bool
    {
        return $this->owns($user, $company);
    }

    public function update(User $user, MonitoredCompany $company): bool
    {
        return $this->owns($user, $company);
    }

    public function delete(User $user, MonitoredCompany $company): bool
    {
        return $this->owns($user, $company);
    }

    /**
     * A company belongs to the user's organization when its portfolio does.
     *
     * The portfolio's organization is read bypassing the tenant scope, so a
     * cross-tenant company (reachable by id, since companies aren't globally
     * scoped) is correctly denied rather than throwing.
     */
    private function owns(User $user, MonitoredCompany $company): bool
    {
        if ($user->organization_id === null) {
            return false;
        }

        $organizationId = Portfolio::withoutGlobalScopes()
            ->whereKey($company->portfolio_id)
            ->value('organization_id');

        return $organizationId === $user->organization_id;
    }
}
