<?php

namespace App\Policies;

use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDayCareDocument;
use App\Models\User;

class ApplicationDraftDayCareDocumentPolicy
{
  public function view(User $user, ApplicationDraftDayCareDocument $document, ApplicationDraft $draft)
  {
    return $user->ownsApplicationDraft($draft);
  }

  public function create(User $user, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }

  public function delete(User $user, ApplicationDraftDayCareDocument $document, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }
}
