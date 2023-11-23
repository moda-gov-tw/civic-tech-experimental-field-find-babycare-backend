<?php

namespace App\Policies;

use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftInfantDocument;
use App\Models\User;

class ApplicationDraftInfantDocumentPolicy
{
  public function view(User $user, ApplicationDraftInfantDocument $document, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }

  public function create(User $user, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }

  public function delete(User $user, ApplicationDraftInfantDocument $document, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }
}
