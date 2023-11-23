<?php

namespace App\Policies;

use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDocument;
use App\Models\User;

class ApplicationDraftDocumentPolicy
{
  public function view(User $user, ApplicationDraftDocument $document, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }

  public function create(User $user, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }

  public function delete(User $user, ApplicationDraftDocument $document, ApplicationDraft $draft): bool
  {
    return $user->ownsApplicationDraft($draft);
  }
}
