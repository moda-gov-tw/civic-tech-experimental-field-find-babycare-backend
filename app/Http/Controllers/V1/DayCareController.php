<?php

namespace App\Http\Controllers\V1;

use App\Enums\DayCareCategory;
use App\Enums\DayCareType;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DayCareResource;
use App\Models\Address;
use App\Models\DayCare;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;

class DayCareController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', DayCare::class);

        return DayCareResource::collection($request->user()->dayCares()->paginate());
    }

    public function store(Request $request)
    {
        $this->authorize('create', DayCare::class);

        $validated = $request->validate([
            'name' => 'required|string',
            'type' => ['required', new Enum(DayCareType::class)],
            'is_in_construction' => 'required|boolean',
            'is_in_raffle' => 'required|boolean',
            'is_accepting_applications' => 'required|boolean',
            'category' => ['required', new Enum(DayCareCategory::class)],
            'operating_hours' => 'required|string',
            'capacity' => 'required|integer',
            'monthly_fees' => 'required|numeric',
            'establishment_id' => 'required|string',
            'phone' => 'required|string',
            'fax' => 'required|string',
            'email' => 'required|email',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'facebook_page_url' => 'required|url',
            'address.city' => 'required|string',
            'address.district' => 'required|string',
            'address.street' => 'required|string'
        ]);

        $address = Address::create(Arr::pull($validated, 'address'));

        $validated['address_id'] = $address->id;

        return new DayCareResource(DayCare::create($validated));
    }

    public function show(DayCare $dayCare)
    {
        $this->authorize('view', $dayCare);

        return new DayCareResource($dayCare);
    }

    public function update(Request $request, DayCare $dayCare)
    {
        $this->authorize('update', $dayCare);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'type' => ['sometimes', new Enum(DayCareType::class)],
            'is_in_construction' => 'sometimes|boolean',
            'is_in_raffle' => 'sometimes|boolean',
            'is_accepting_applications' => 'sometimes|boolean',
            'category' => ['sometimes', new Enum(DayCareCategory::class)],
            'operating_hours' => 'sometimes|string',
            'capacity' => 'sometimes|integer',
            'monthly_fees' => 'sometimes|numeric',
            'establishment_id' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'fax' => 'sometimes|string',
            'email' => 'sometimes|email',
            'lat' => 'sometimes|numeric',
            'lon' => 'sometimes|numeric',
            'facebook_page_url' => 'sometimes|url',
            'address.city' => 'sometimes|string',
            'address.district' => 'sometimes|string',
            'address.street' => 'sometimes|string'
        ]);

        $dayCare->update($validated);
    }

    public function destroy(DayCare $dayCare)
    {
        $this->authorize('delete', $dayCare);

        $dayCare->delete();
    }
}
