<?php

namespace App\Policies;

use App\Enums\AdministrativeGroupMemberRole;
use App\Models\AdministrativeGroup;
use App\Models\User;

class AdministrativeGroupMemberPolicy
{
  public function before(User $user): bool | null
  {
    return $user->isSuperUser() ? true : null;
  }

  public function viewAny(User $user, AdministrativeGroup $group): bool
  {
    return $user->isInAdministrativeGroup($group);
  }

  public function view(User $user, AdministrativeGroup $group): bool
  {
    return $user->isInAdministrativeGroup($group);
  }

  public function create(User $user, AdministrativeGroup $group): bool
  {
    return $user->isInAdministrativeGroup($group, AdministrativeGroupMemberRole::Administrator);
  }

  public function update(User $user, AdministrativeGroup $group): bool
  {
    return $user->isInAdministrativeGroup($group, AdministrativeGroupMemberRole::Administrator);
  }

  public function delete(User $user, AdministrativeGroup $group): bool
  {
    return $user->isInAdministrativeGroup($group, AdministrativeGroupMemberRole::Administrator);
  }
}
