<?php

namespace App\Http\Controllers\V1;

use App\Enums\DayCareType;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationDraftDayCareResource;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDayCare;
use App\Models\DayCare;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationDraftDayCareController extends Controller
{
  public function index(ApplicationDraft $draft)
  {
    $this->authorize('viewAny', [ApplicationDraftDayCare::class, $draft]);

    return ApplicationDraftDayCareResource::collection($draft->dayCares()->paginate());
  }

  public function store(Request $request, ApplicationDraft $draft)
  {
    $this->authorize('create', [ApplicationDraftInfant::class, $draft]);

    $validated = $request->validate([
      'day_care_id' => [
        'required',
        Rule::exists('day_cares', 'id')->where(
          fn (Builder $query) => $query->where('type', DayCareType::Public)
        )
      ]
    ]);

    $draft->dayCares()->attach($validated['day_care_id']);

    return (new ApplicationDraftDayCareResource(
      $draft->dayCares()->find($validated['day_care_id'])
    ))->response()->setStatusCode(JsonResponse::HTTP_CREATED);
  }

  public function show(ApplicationDraft $draft, DayCare $dayCare)
  {
    $this->authorize('view', [$dayCare->pivot, $draft]);

    return new ApplicationDraftDayCareResource($dayCare);
  }

  public function update(Request $request, ApplicationDraft $draft, DayCare $dayCare)
  {
    $this->authorize('update', [$dayCare->pivot, $draft]);

    $validated = $request->validate([]);

    $dayCare->pivot->update($validated);

    return new ApplicationDraftDayCareResource($dayCare);
  }

  public function destroy(ApplicationDraft $draft, DayCare $dayCare)
  {
    $this->authorize('delete', [$dayCare->pivot, $draft]);

    $dayCare->pivot->delete();

    return response()->noContent();
  }
}
