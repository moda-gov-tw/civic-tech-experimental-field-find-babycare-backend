<?php

namespace App\Policies;

use App\Enums\DayCareMemberRole;
use App\Models\DayCare;
use App\Models\User;

class DayCareMemberPolicy
{
  public function before(User $user): bool | null
  {
    return $user->isSuperUser() ? true : null;
  }

  public function viewAny(User $user, DayCare $dayCare): bool
  {
    return $user->isInDayCare($dayCare);
  }

  public function view(User $user, DayCare $dayCare): bool
  {
    return $user->isInDayCare($dayCare);
  }

  public function create(User $user, DayCare $dayCare): bool
  {
    return $user->isInDayCare($dayCare, DayCareMemberRole::Administrator);
  }

  public function update(User $user, DayCare $dayCare): bool
  {
    return $user->isInDayCare($dayCare, DayCareMemberRole::Administrator);
  }

  public function delete(User $user, DayCare $dayCare): bool
  {
    return $user->isInDayCare($dayCare, DayCareMemberRole::Administrator);
  }
}
