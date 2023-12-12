<?php

namespace App\Http\Controllers\V1;

use App\Enums\InfantSex;
use App\Enums\InfantStatusType;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationDraftInfantResource;
use App\Models\Address;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftInfant;
use App\Models\Infant;
use Arr;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ApplicationDraftInfantController extends Controller
{
  public function index(ApplicationDraft $draft)
  {
    $this->authorize('viewAny', [ApplicationDraftInfant::class, $draft]);

    return ApplicationDraftInfantResource::collection(
      $draft->infants()->with('statuses', 'address')->paginate()
    );
  }

  public function store(Request $request, ApplicationDraft $draft)
  {
    $this->authorize('create', [ApplicationDraftInfant::class, $draft]);

    $validated = $request->validate([
      'sex' => ['required', new Enum(InfantSex::class)],
      'name' => 'required|string',
      'id_number' => 'required|string',
      'dob' => 'required|date',
      'medical_conditions' => 'sometimes|string',
      'address' => 'sometimes|array',
      'address.city' => 'required_with:address|string',
      'address.district' => 'required_with:address|string',
      'address.street' => 'required_with:address|string',
      'statuses' => 'sometimes|array',
      'statuses.*' => ['required_with:statuses', 'distinct', new Enum(InfantStatusType::class)],
    ]);

    $validatedAddress = Arr::pull($validated, 'address');

    $validatedStatuses = Arr::pull($validated, 'statuses');

    return DB::transaction(function () use ($draft, $validated, $validatedAddress, $validatedStatuses) {
      if (isset($validatedAddress)) {
        $validated['address_id'] = Address::create($validatedAddress)->id;
      }

      $infant = $draft->infants()->create($validated);

      if ($validatedStatuses) {
        $infant->statuses()->createMany(
          collect($validatedStatuses)
            ->map(fn ($status) => ['type' => $status])
            ->toArray()
        );
      }

      $infant->load('statuses', 'address');

      return new ApplicationDraftInfantResource($infant);
    });
  }

  public function show(ApplicationDraft $draft, Infant $infant)
  {
    $this->authorize('view', [$infant->pivot, $draft]);

    return new ApplicationDraftInfantResource($infant);
  }

  public function update(Request $request, ApplicationDraft $draft, Infant $infant)
  {
    $this->authorize('update', [$infant->pivot, $draft]);

    $validated = $request->validate([
      'sex' => ['sometimes', new Enum(InfantSex::class)],
      'name' => 'sometimes|string',
      'id_number' => 'sometimes|string',
      'dob' => 'sometimes|date',
      'medical_conditions' => 'sometimes|string',
      'address' => 'sometimes|array',
      'address.city' => 'required_with:address|string',
      'address.district' => 'required_with:address|string',
      'address.street' => 'required_with:address|string',
      'statuses' => 'sometimes|array',
      'statuses.*' => ['required_with:statuses', 'distinct', new Enum(InfantStatusType::class)],
    ]);

    $validatedAddress = Arr::pull($validated, 'address');

    $validatedStatuses = Arr::pull($validated, 'statuses');

    return DB::transaction(function () use ($infant, $validated, $validatedAddress, $validatedStatuses) {
      if ($validatedAddress) {
        $validated['address_id'] = $infant->address()->updateOrCreate($validatedAddress)->id;
      }

      if ($validatedStatuses) {
        $infant->statuses()->delete();

        $infant->statuses()->createMany(
          collect($validatedStatuses)
            ->map(fn ($status) => ['type' => $status])
            ->toArray()
        );
      }

      $infant->update($validated);

      $infant->load('statuses', 'address');

      return new ApplicationDraftInfantResource($infant);
    });
  }

  public function destroy(ApplicationDraft $draft, Infant $infant)
  {
    $this->authorize('delete', [$infant->pivot, $draft]);

    return DB::transaction(function () use ($infant) {
      $infant->statuses()->delete();

      $infant->delete();

      $infant->address()->delete();

      return response()->noContent();
    });
  }
}
