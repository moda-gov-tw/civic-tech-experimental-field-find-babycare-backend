<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\ApplicationInfantDocument;
use App\Models\User;

class ApplicationInfantDocumentPolicy
{
  public function view(User $user, ApplicationInfantDocument $document, Application $application): bool
  {
    return $user->ownsApplication($application) || $user->isInDayCare($application->dayCare);
  }
}
