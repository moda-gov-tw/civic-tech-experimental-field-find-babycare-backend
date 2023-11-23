<?php

namespace App\Policies;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\DayCare;
use App\Models\User;

class ApplicationPolicy
{
    public function dayCareViewAny(User $user, DayCare $dayCare): bool
    {
        return $user->isInDayCare($dayCare);
    }

    public function dayCareView(User $user, DayCare $dayCare): bool
    {
        return $user->isInDayCare($dayCare);
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Application $application): bool
    {
        return true;
    }

    public function update(User $user, Application $application): bool
    {
        return false;
    }

    public function delete(User $user, Application $application): bool
    {
        return false;
    }

    public function restore(User $user, Application $application): bool
    {
        return false;
    }

    public function forceDelete(User $user, Application $application): bool
    {
        return false;
    }

    public function withdraw(User $user, Application $application): bool
    {
        return (
            $user->ownsApplication($application)
            && (
                $application->status === ApplicationStatus::WaitlistApproved
                || $application->status === ApplicationStatus::RaffleApproved
            )
        );
    }

    public function forfeit(User $user, Application $application): bool
    {
        return (
            $user->ownsApplication($application)
            && (
                $application->status === ApplicationStatus::Accepted
                || $application->status === ApplicationStatus::RegistrationReturned
            )
        );
    }

    public function resubmit(User $user, Application $application): bool
    {
        return (
            $user->ownsApplication($application)
            && (
                $application->status === ApplicationStatus::ApplicationReturned
                || $application->status === ApplicationStatus::RegistrationReturned
            )
        );
    }

    public function register(User $user, Application $application): bool
    {
        return (
            $user->ownsApplication($application)
            && $application->status === ApplicationStatus::Accepted
        );
    }

    public function return(User $user, DayCare $dayCare, Application $application): bool
    {
        return (
            $user->isInDayCare($dayCare)
            && (
                $application->status === ApplicationStatus::Submitted
                || $application->status === ApplicationStatus::Registered
            )
        );
    }

    public function approve(User $user, DayCare $dayCare, Application $application): bool
    {
        return (
            $user->isInDayCare($dayCare)
            && $application->status === ApplicationStatus::Submitted
        );
    }

    public function accept(User $user, DayCare $dayCare, Application $application): bool
    {
        return (
            $user->isInDayCare($dayCare)
            && (
                $application->status === ApplicationStatus::WaitlistApproved
                || $application->status === ApplicationStatus::RaffleApproved
            )
        );
    }

    public function reject(User $user, DayCare $dayCare, Application $application): bool
    {
        return (
            $user->isInDayCare($dayCare)
            && (
                $application->status === ApplicationStatus::WaitlistApproved
                || $application->status === ApplicationStatus::RaffleApproved
                || $application->status === ApplicationStatus::Registered
            )
        );
    }

    public function enroll(User $user, DayCare $dayCare, Application $application): bool
    {
        return (
            $user->isInDayCare($dayCare)
            && $application->status === ApplicationStatus::Registered
        );
    }
}
