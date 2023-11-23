<?php

namespace App\Http\Controllers\V1;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationDraftResource;
use App\Models\Address;
use App\Models\ApplicationDraft;
use App\Models\Contact;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationDraftController extends Controller
{
    public function index(Request $request)
    {
        return ApplicationDraftResource::collection($request->user()->applicationDrafts()->paginate());
    }

    public function store(Request $request)
    {
        $this->authorize('create', ApplicationDraft::class);

        $validated = $request->validate([
            'applicant' => 'sometimes',
            'applicant.relationship_with_infant' => 'required_with:applicant|string',
            'applicant.name' => 'required_with:applicant|string',
            'applicant.phone' => 'required_with:applicant|string',
            'applicant.is_living_in_the_same_household' => 'required_with:applicant|boolean',
            'applicant.address' => 'sometimes|array',
            'applicant.address.city' => 'required_with:address|string',
            'applicant.address.district' => 'required_with:address|string',
            'applicant.address.street' => 'required_with:address|string',
        ]);

        $validatedApplicant = Arr::pull($validated, 'applicant');

        if (isset($validatedApplicant)) {
            $validated['contact_id'] = Contact::create([
                'name' => $validatedApplicant['name'],
                'phone' => $validatedApplicant['phone'],
                'relationship_with_infant' => $validatedApplicant['relationship_with_infant'],
                'is_living_in_the_same_household' => $validatedApplicant['is_living_in_the_same_household'],
                'address_id' => Address::create($validatedApplicant['address'])->id
            ])->id;
        }

        $draft = $request->user()->applicationDrafts()->create($validated);

        return new ApplicationDraftResource($draft->load('applicant.address'));
    }

    public function show(ApplicationDraft $draft)
    {
        $this->authorize('view', $draft);

        return new ApplicationDraftResource($draft);
    }

    public function update(Request $request, ApplicationDraft $draft)
    {
        $this->authorize('update', $draft);

        $validated = $request->validate([
            'applicant' => 'sometimes',
            'applicant.relationship_with_infant' => 'required_with:applicant|string',
            'applicant.name' => 'required_with:applicant|string',
            'applicant.phone' => 'required_with:applicant|string',
            'applicant.is_living_in_the_same_household' => 'required_with:applicant|boolean',
            'applicant.address' => 'sometimes|array',
            'applicant.address.city' => 'required_with:address|string',
            'applicant.address.district' => 'required_with:address|string',
            'applicant.address.street' => 'required_with:address|string',
        ]);

        $validatedApplicant = Arr::pull($validated, 'applicant');

        if (isset($validatedApplicant)) {
        }

        $draft->update($validated);
    }

    public function destroy(ApplicationDraft $draft)
    {
        $this->authorize('delete', $draft);

        $draft->delete();
    }

    public function submit(Request $request, ApplicationDraft $draft)
    {
        $this->authorize('submit', $draft);

        DB::transaction(function () use ($request, $draft) {
            $draft = ApplicationDraft::where('id', $draft->id)
                ->with([
                    'daycares',
                    'infants',
                    'documents',
                    'dayCareDocuments',
                    'infantDocuments'
                ])
                ->lockForUpdate()
                ->first();


            foreach ($draft->infants as $infant) {
                foreach ($draft->dayCares as $dayCare) {
                    $application = $request->user()->applications()->create([
                        'day_care_id' => $dayCare->id,
                        'infant_id' => $infant->id,
                        'status' => ApplicationStatus::Submitted
                    ]);

                    // TODO unfortunately, createMany insert entries one by one
                    // use insert on the model itself instead, while making sure to add the required timestamps
                    // https://stackoverflow.com/questions/12702812/bulk-insertion-in-laravel-using-eloquent-orm

                    $application->documents()->createMany(
                        $draft->documents
                            ->map
                            ->only('type', 'path')
                    );

                    $application->dayCareDocuments()->createMany(
                        $draft->dayCareDocuments
                            ->filter(fn ($dayCareDocument) => $dayCareDocument->day_care_id === $dayCare->id)
                            ->map
                            ->only('type', 'path')
                    );

                    $application->infantDocuments()->createMany(
                        $draft->infantDocuments
                            ->filter(fn ($infantDocument) => $infantDocument->infant_id === $infant->id)
                            ->map
                            ->only('type', 'path')
                    );
                }
            }

            $draft->delete();
        });
    }
}
