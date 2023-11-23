<?php

namespace App\Policies;

use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDayCare;
use App\Models\User;

class ApplicationDraftDayCarePolicy
{
    public function viewAny(User $user, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function view(User $user, ApplicationDraftDayCare $dayCare,  ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function create(User $user, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function update(User $user, ApplicationDraftDayCare $dayCare, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function delete(User $user, ApplicationDraftDayCare $dayCare, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }
}
