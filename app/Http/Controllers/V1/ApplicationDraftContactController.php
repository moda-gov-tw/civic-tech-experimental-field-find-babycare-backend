<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationDraftContactResource;
use App\Models\Address;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftContact;
use App\Models\Contact;
use Arr;
use DB;
use Illuminate\Http\Request;

class ApplicationDraftContactController extends Controller
{
    public function index(ApplicationDraft $draft)
    {
        $this->authorize('viewAny', [ApplicationDraftContact::class, $draft]);

        return ApplicationDraftContactResource::collection(
            $draft->contacts()->with('address')->paginate()
        );
    }

    public function store(Request $request, ApplicationDraft $draft)
    {
        $this->authorize('create', [ApplicationDraftContact::class, $draft]);

        $validated = $request->validate([
            'relationship_with_infant' => 'required|string',
            'name' => 'required|string',
            'phone' => 'required|string',
            'is_living_in_the_same_household' => 'required|boolean',
            'address' => 'sometimes|array',
            'address.city' => 'required_with:address|string',
            'address.district' => 'required_with:address|string',
            'address.street' => 'required_with:address|string',
        ]);

        $validatedAddress = Arr::pull($validated, 'address');

        return DB::transaction(function () use ($draft, $validated, $validatedAddress) {
            if (isset($validatedAddress)) {
                $validated['address_id'] = Address::create($validatedAddress)->id;
            }

            return new ApplicationDraftContactResource(
                $draft->contacts()
                    ->create($validated)
                    ->load('address')
            );
        });
    }

    public function show(ApplicationDraft $draft, Contact $contact)
    {
        $this->authorize('view', [$contact->pivot, $draft]);

        return new ApplicationDraftContactResource($contact->load('address'));
    }

    public function update(Request $request, ApplicationDraft $draft, Contact $contact)
    {
        $this->authorize('update', [$contact->pivot, $draft]);

        $validated = $request->validate([
            'relationship_with_infant' => 'sometimes|string',
            'name' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'is_living_in_the_same_household' => 'sometimes|boolean',
            'address' => 'sometimes|array',
            'address.city' => 'required_with:address|string',
            'address.district' => 'required_with:address|string',
            'address.street' => 'required_with:address|string',
        ]);

        $validatedAddress = Arr::pull($validated, 'address');

        return DB::transaction(function () use ($contact, $validated, $validatedAddress) {
            if (isset($validatedAddress)) {
                $validated['address_id'] = $contact->address()->updateOrCreate($validatedAddress)->id;
            }

            $contact->update($validated);

            return new ApplicationDraftContactResource($contact->load('address'));
        });
    }

    public function destroy(ApplicationDraft $draft, Contact $contact)
    {
        $this->authorize('delete', [$contact->pivot, $draft]);

        return DB::transaction(function () use ($draft, $contact) {
            $contact->pivot->delete();

            $contact->delete();

            return response()->noContent();
        });
    }
}
