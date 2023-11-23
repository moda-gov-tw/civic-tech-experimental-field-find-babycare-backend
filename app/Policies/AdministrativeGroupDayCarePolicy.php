<?php

namespace App\Policies;

use App\Models\AdministrativeGroup;
use App\Models\User;

class AdministrativeGroupDayCarePolicy
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
    return false;
  }

  public function delete(User $user, AdministrativeGroup $group): bool
  {
    return false;
  }
}
