<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\User;

class ApplicationDocumentPolicy
{
  public function view(User $user, ApplicationDocument $document, Application $application): bool
  {
    return $user->ownsApplication($application) || $user->isInDayCare($application->dayCare);
  }
}
