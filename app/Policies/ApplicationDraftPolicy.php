<?php

namespace App\Policies;

use App\Models\ApplicationDraft;
use App\Models\User;

class ApplicationDraftPolicy
{
    public function view(User $user, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function delete(User $user, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }

    public function submit(User $user, ApplicationDraft $draft): bool
    {
        return $user->ownsApplicationDraft($draft);
    }
}
