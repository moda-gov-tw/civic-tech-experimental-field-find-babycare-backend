<?php

namespace App\Policies;

use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftContact;
use App\Models\User;

class ApplicationDraftContactPolicy
{
  public function viewAny(User $user, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }

  public function view(User $user, ApplicationDraftContact $contact, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }

  public function create(User $user, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }

  public function update(User $user, ApplicationDraftContact $contact, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }

  public function delete(User $user, ApplicationDraftContact $contact, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }
}
