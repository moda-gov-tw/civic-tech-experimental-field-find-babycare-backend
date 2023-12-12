<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdministrativeGroupDayCareResource;
use App\Models\AdministrativeGroup;
use App\Models\AdministrativeGroupDayCare;
use App\Models\DayCare;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdministrativeGroupDayCareController extends Controller
{
  public function index(Request $request, AdministrativeGroup $group)
  {
    $this->authorize('viewAny', [AdministrativeGroupDayCare::class, $group]);

    return AdministrativeGroupDayCareResource::collection(
      $group->dayCares()->paginate()
    );
  }

  public function store(Request $request, AdministrativeGroup $group)
  {
    $this->authorize('create', [AdministrativeGroupDayCare::class, $group]);

    $validated = $request->validate([
      'day_care_id' => 'required|int|exists:day_cares,id'
    ]);

    $dayCare = DayCare::where('id', $validated['day_care_id'])->first();

    $group->dayCares()->attach($dayCare);

    return (new AdministrativeGroupDayCareResource($dayCare))
      ->response()
      ->setStatusCode(JsonResponse::HTTP_CREATED);
  }

  public function show(AdministrativeGroup $group, DayCare $dayCare)
  {
    $this->authorize('view', [AdministrativeGroupDayCare::class, $group]);

    return new AdministrativeGroupDayCareResource($dayCare);
  }

  public function destroy(AdministrativeGroup $group, DayCare $dayCare)
  {
    $this->authorize('delete', [AdministrativeGroupDayCare::class, $group]);

    $dayCare->pivot->delete();

    return response()->noContent();
  }
}
