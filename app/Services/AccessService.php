<?php

namespace App\Services;

use App\Exceptions\RuleViolation;
use App\Models\Role;
use App\Models\User;

/** Stops anyone from handing out or taking over more access than they have themselves. */
class AccessService
{
    /**
     * @param  array<int, string>  $routes
     */
    public function assertHolds(User $actor, array $routes): void
    {
        if ($actor->isSuperAdmin()) {
            return;
        }

        if (array_diff($routes, $actor->role?->routes() ?? [])) {
            throw new RuleViolation('You cannot give or change access that you do not have yourself.');
        }
    }

    public function assertCanManageRole(User $actor, Role $role): void
    {
        if ($actor->isSuperAdmin()) {
            return;
        }

        if ($role->is_super) {
            throw new RuleViolation('Only an administrator can do this.');
        }

        $this->assertHolds($actor, $role->routes());
    }
}
