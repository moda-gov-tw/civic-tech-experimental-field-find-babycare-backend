<?php

namespace App\Policies;

use App\Enums\AdministrativeGroupMemberRole;
use App\Models\AdministrativeGroup;
use App\Models\User;

class AdministrativeGroupPolicy
{
    public function before(User $user): bool | null
    {
        return $user->isSuperUser() ? true : null;
    }

    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, AdministrativeGroup $group): bool
    {
        return $user->isInAdministrativeGroup($group);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AdministrativeGroup $group): bool
    {
        return $user->isInAdministrativeGroup($group, AdministrativeGroupMemberRole::Administrator);
    }

    public function delete(User $user, AdministrativeGroup $group): bool
    {
        return false;
    }
}
