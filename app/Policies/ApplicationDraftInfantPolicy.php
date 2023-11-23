<?php

namespace App\Policies;

use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftInfant;
use App\Models\User;

class ApplicationDraftInfantPolicy
{
    public function viewAny(User $user, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function view(User $user, ApplicationDraftInfant $infant,  ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function create(User $user, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function update(User $user, ApplicationDraftInfant $infant, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function delete(User $user, ApplicationDraftInfant $infant, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }
}
