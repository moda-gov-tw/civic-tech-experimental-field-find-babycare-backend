<?php

namespace App\Http\Controllers\V1;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationResource;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Application::class);

        return ApplicationResource::collection($request->user()->applications()->paginate());
    }

    public function show(Application $application)
    {
        $this->authorize('view', $application);

        return new ApplicationResource($application);
    }

    public function update(Request $request, Application $application)
    {
        $this->authorize('update', $application);

        $validated = $request->validate([]);

        $application->update($validated);
    }

    public function destroy(Application $application)
    {
        $this->authorize('delete', $application);

        $application->delete();
    }

    public function withdraw(Application $application)
    {
        $this->authorize('withdraw', $application);

        $application->update([
            'status' => ApplicationStatus::Withdrew
        ]);
    }

    public function forfeit(Application $application)
    {
        $this->authorize('forfeit', $application);

        $application->update([
            'status' => ApplicationStatus::Forfeited
        ]);
    }

    public function resubmit(Application $application)
    {
        $this->authorize('resubmit', $application);

        $application->update([
            'status' => $application->status === ApplicationStatus::ApplicationReturned
                ? ApplicationStatus::Submitted
                : ApplicationStatus::Registered
        ]);
    }

    public function register(Application $application)
    {
        $this->authorize('register', $application);
    }
}
