<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\ApplicationDayCareDocument;
use App\Models\User;

class ApplicationDayCareDocumentPolicy
{
  public function view(User $user, ApplicationDayCareDocument $document, Application $application): bool
  {
    return $user->ownsApplication($application) || $user->isInDayCare($application->dayCare);
  }
}
