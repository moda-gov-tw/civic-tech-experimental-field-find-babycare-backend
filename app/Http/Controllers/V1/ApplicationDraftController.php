<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreApplicationDraftRequest;
use App\Http\Requests\V1\UpdateApplicationDraftRequest;
use App\Http\Resources\V1\ApplicationDraftResource;
use App\Models\ApplicationDraft;
use App\Services\ApplicationDraftService;
use Illuminate\Http\Request;

class ApplicationDraftController extends Controller
{
    public function __construct(private ApplicationDraftService $service)
    {
    }

    public function index(Request $request)
    {
        return ApplicationDraftResource::collection($request->user()->applicationDrafts()->paginate());
    }

    public function store(StoreApplicationDraftRequest $request)
    {
        $validated = $request->validate([]);

        $draft = $request->user()->applicationDrafts()->create($validated);

        return new ApplicationDraftResource($draft->load('applicant.address'));
    }

    public function show(ApplicationDraft $draft)
    {
        $this->authorize('view', $draft);

        $draft->load('applicant.address', 'infants.address', 'contacts.address');

        return new ApplicationDraftResource($draft);
    }

    public function update(UpdateApplicationDraftRequest $request, ApplicationDraft $draft)
    {
        $validated = $request->validated();

        $this->service->update($draft, $validated);

        return response()->noContent();
    }

    public function destroy(ApplicationDraft $draft)
    {
        $this->authorize('delete', $draft);

        $draft->delete();

        return response()->noContent();
    }

    public function submit(Request $request, ApplicationDraft $draft)
    {
        $this->authorize('submit', $draft);

        $this->service->submit($draft);

        return response()->noContent();
    }
}
