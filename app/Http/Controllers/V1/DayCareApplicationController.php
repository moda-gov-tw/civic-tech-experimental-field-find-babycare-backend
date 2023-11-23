<?php

namespace App\Http\Controllers\V1;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationResource;
use App\Models\Application;
use App\Models\DayCare;
use Illuminate\Http\Request;

class DayCareApplicationController extends Controller
{
    public function index(DayCare $dayCare)
    {
        $this->authorize('dayCareViewAny', [Application::class, $dayCare]);

        return ApplicationResource::collection($dayCare->applications()->paginate());
    }

    public function show(DayCare $dayCare, Application $application)
    {
        $this->authorize('dayCareView', [Application::class, $dayCare]);

        return new ApplicationResource($application);
    }

    public function return(DayCare $dayCare, Application $application)
    {
        $this->authorize('return', [Application::class, $dayCare, $application]);

        $application->update([
            'status' => $application->status === ApplicationStatus::Submitted
                ? ApplicationStatus::ApplicationReturned
                : ApplicationStatus::RegistrationReturned
        ]);
    }

    public function approve(DayCare $dayCare, Application $application)
    {
        $this->authorize('approve', [Application::class, $dayCare, $application]);

        $application->update([
            'status' => $dayCare->isInRaffle()
                ? ApplicationStatus::RaffleApproved
                : ApplicationStatus::WaitlistApproved
        ]);
    }

    public function accept(DayCare $dayCare, Application $application)
    {
        $this->authorize('accept', [Application::class, $dayCare, $application]);

        $application->update([
            'status' => ApplicationStatus::Accepted
        ]);
    }

    public function reject(DayCare $dayCare, Application $application)
    {
        $this->authorize('reject', [Application::class, $dayCare, $application]);

        $application->update([
            'status' => ApplicationStatus::Rejected
        ]);
    }

    public function enroll(DayCare $dayCare, Application $application)
    {
        $this->authorize('enroll', [Application::class, $dayCare, $application]);
    }
}
