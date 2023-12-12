<?php

use App\Enums\ApplicationStatus;
use App\Enums\InfantSex;
use App\Enums\InfantStatusType;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftContact;
use App\Models\ApplicationDraftDayCareDocument;
use App\Models\ApplicationDraftDocument;
use App\Models\ApplicationDraftInfantDocument;
use App\Models\Contact;
use App\Models\DayCare;
use App\Models\Infant;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelMissing;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

describe('index', function () {
    test('users can get their application drafts', function () {
        $user = User::factory()->create();

        $drafts = ApplicationDraft::factory()->for($user)->count(4)->create();

        actingAs($user)
            ->getJson(route('application-drafts.index'))
            ->assertOk()
            ->assertJson([
                'data' => $drafts->only('id')->toArray()
            ]);
    });
});

describe('store', function () {
    test('users can create application drafts', function () {
        $user = User::factory()->create();

        actingAs($user)
            ->postJson(route('application-drafts.store'))
            ->assertCreated();
    });
});

describe('show', function () {
    test('users can get one of their application drafts', function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->for($user)->create();

        actingAs($user)
            ->getJson(route('application-drafts.show', $draft))
            ->assertOk()
            ->assertJson([
                'data' => $draft->only('id')
            ]);
    });

    test("users can't see another user's application drafts", function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->create();

        actingAs($user)
            ->getJson(route('application-drafts.show', $draft))
            ->assertForbidden();
    });
});

describe('update', function () {
    test('users can update applicant', function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->for($user)->create();

        $name = 'Applicant name';

        $city = 'City';

        actingAs($user)
            ->patchJson(route('application-drafts.update', $draft), [
                'applicant' => [
                    'relationship_with_infant' => 'Parent',
                    'name' => $name,
                    'phone' => '123456789',
                    'is_living_in_the_same_household' => true,
                    'address' => [
                        'city' => $city,
                        'district' => 'District',
                        'street' => 'Street'
                    ]
                ]
            ])
            ->assertOk();

        assertTrue(
            $draft->refresh()
                ->applicant()
                ->where('name', $name)
                ->whereHas('address', fn ($query) => $query->where('city', $city))
                ->exists()
        );
    });

    test("users can update infants", function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->for($user)->create();

        $firstName = 'First infant name';

        $firstCity = 'First city';

        $firstStatues = [
            InfantStatusType::BigFamily,
            InfantStatusType::Challenged
        ];

        $secondName = 'Second infant name';

        actingAs($user)
            ->patchJson(route('application-drafts.update', $draft), [
                'infants' => [
                    [
                        'sex' => InfantSex::Female,
                        'name' => $firstName,
                        'id_number' => '123456789',
                        'dob' => '2020-01-01',
                        'medical_conditions' => 'Medical conditions',
                        'address' => [
                            'city' => $firstCity,
                            'district' => 'District',
                            'street' => 'Street'
                        ],
                        'statuses' => $firstStatues
                    ],
                    [
                        'sex' => InfantSex::Male,
                        'name' => $secondName,
                        'id_number' => '123456789',
                        'dob' => '2020-01-01',
                        'medical_conditions' => 'Medical conditions',
                    ]
                ]
            ])
            ->assertOk();

        assertEquals(2, $draft->infants()->count());

        assertTrue(
            $draft->infants()
                ->where('name', $firstName)
                ->whereHas('address', fn ($query) => $query->where('city', $firstCity))
                ->whereHas('statuses', fn ($query) => $query->whereIn('type', $firstStatues))
                ->exists()
        );

        assertTrue(
            $draft->infants()
                ->where('name', $secondName)
                ->whereDoesntHave('address')
                ->whereDoesntHave('statuses')
                ->exists()
        );
    });

    test("users can update existing infants", function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->for($user)->create();

        $infants = Infant::factory()->count(3)->hasAttached($draft)->create();

        $firstAddress = $infants[0]->address;
        $thirdAddress = $infants[2]->address;

        $firstName = 'First infant name';

        $firstStatues = [
            InfantStatusType::BigFamily,
            InfantStatusType::Challenged
        ];

        $secondName = 'Second infant name';

        $secondCity = 'Second city';

        actingAs($user)
            ->patchJson(route('application-drafts.update', $draft), [
                'infants' => [
                    [
                        'sex' => InfantSex::Male,
                        'name' => $firstName,
                        'id_number' => '123456789',
                        'dob' => '2020-01-01',
                        'medical_conditions' => 'Medical conditions',
                        'statuses' => $firstStatues
                    ],
                    [
                        'sex' => InfantSex::Female,
                        'name' => $secondName,
                        'id_number' => '123456789',
                        'dob' => '2020-01-01',
                        'medical_conditions' => 'Medical conditions',
                        'address' => [
                            'city' => $secondCity,
                            'district' => 'District',
                            'street' => 'Street'
                        ],
                    ]
                ]
            ])
            ->assertOk();

        assertEquals(2, $draft->infants()->count()); // make sure we have the right number of infants

        assertModelMissing($infants[2]); // since we pass 2 infants, make sure the third one is deleted

        assertTrue(
            $draft->infants()
                ->where('name', $firstName) // make sure the infant is updated
                ->where('address_id', null) // make sure the address is deleted
                ->whereHas('statuses', fn ($query) => $query->whereIn('type', $firstStatues)) // make sure the statuses are updated
                ->exists()
        );

        assertTrue(
            $draft->infants()
                ->where('name', $secondName) // make sure the infant is deleted
                ->whereHas('address', fn ($query) => $query->where('city', $secondCity)) // make sure the address is updated
                ->doesntHave('statuses') // make sure the statuses are deleted
                ->exists()
        );

        // make sure there are no dangling addresses
        assertModelMissing($firstAddress);
        assertModelMissing($thirdAddress);
    });

    test("users can update contacts", function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->for($user)->create();

        $firstName = 'First contact';

        $firstCity = 'City';

        $secondName = 'Second contact';

        actingAs($user)
            ->patchJson(route('application-drafts.update', $draft), [
                'contacts' => [
                    [
                        'relationship_with_infant' => 'Parent',
                        'name' => $firstName,
                        'phone' => '123456789',
                        'is_living_in_the_same_household' => true,
                        'address' => [
                            'city' => $firstCity,
                            'district' => 'District',
                            'street' => 'Street'
                        ]
                    ],
                    [
                        'relationship_with_infant' => 'Parent',
                        'name' => $secondName,
                        'phone' => '123456789',
                        'is_living_in_the_same_household' => true,
                    ]
                ]
            ])
            ->assertOk();

        assertEquals(2, $draft->contacts()->count());

        assertTrue(
            $draft->contacts()
                ->where('name', $firstName)
                ->whereHas('address', fn ($query) => $query->where('city', $firstCity))
                ->exists()
        );

        assertTrue(
            $draft->contacts()
                ->where('name', $secondName)
                ->whereDoesntHave('address')
                ->exists()
        );
    });

    test("users can update existing contacts", function () {
        $user = User::factory()->create();

        $contacts = Contact::factory()->count(3)->create();

        $draft = ApplicationDraft::factory()->for($user)->hasAttached($contacts)->create();

        $firstName = 'Contact';

        $firstCity = 'City';

        $secondName = 'Second contact';

        $secondAddress = $contacts[1]->address;

        actingAs($user)
            ->patchJson(route('application-drafts.update', $draft), [
                'contacts' => [
                    [
                        'relationship_with_infant' => 'Parent',
                        'name' => $firstName,
                        'phone' => '123456789',
                        'is_living_in_the_same_household' => true,
                        'address' => [
                            'city' => $firstCity,
                            'district' => 'District',
                            'street' => 'Street'
                        ]
                    ],
                    [
                        'relationship_with_infant' => 'Parent',
                        'name' => $secondName,
                        'phone' => '123456789',
                        'is_living_in_the_same_household' => true,
                    ]
                ]
            ])
            ->assertOk();

        assertEquals(2, $draft->contacts()->count());

        assertModelMissing($contacts[2]);

        assertTrue(
            $draft->contacts()
                ->where('name', $firstName)
                ->whereHas('address', fn ($query) => $query->where('city', $firstCity))
                ->exists()
        );

        assertTrue(
            $draft->contacts()
                ->where('name', $secondName)
                ->whereDoesntHave('address')
                ->exists()
        );

        assertModelMissing($secondAddress);
    });

    test("users can't delete another user's application drafts", function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->create();

        actingAs($user)
            ->patchJson(route('application-drafts.update', $draft))
            ->assertForbidden();
    });
});

describe('destroy', function () {
    test('users can delete one of their application drafts', function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->for($user)->create();

        actingAs($user)
            ->deleteJson(route('application-drafts.destroy', $draft))
            ->assertOk();

        assertModelMissing($draft);
    });

    test("users can't delete another user's application drafts", function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->create();

        actingAs($user)
            ->deleteJson(route('application-drafts.destroy', $draft))
            ->assertForbidden();
    });
});

describe('submit', function () {
    test('user can submit their draft', function () {
        $user = User::factory()->create();

        $dayCares = DayCare::factory()->count(2)->create();

        $infants = Infant::factory()->count(2)->create();

        $applicant = Contact::factory()->create();

        $contacts = Contact::factory()->count(2)->create();

        $draft = ApplicationDraft::factory()
            ->for($user)
            ->for($applicant, 'applicant')
            ->hasAttached($dayCares)
            ->hasAttached($infants)
            ->hasAttached($contacts)
            ->create();

        ApplicationDraftDocument::factory()->for($draft)->count(2)->create();

        foreach ($dayCares as $dayCare) {
            ApplicationDraftDayCareDocument::factory()
                ->for($dayCare)
                ->for($draft)
                ->count(2)
                ->create();
        }

        foreach ($infants as $infant) {
            ApplicationDraftInfantDocument::factory()
                ->for($infant)
                ->for($draft)
                ->count(2)
                ->create();
        }

        $documents = $draft->documents()->get();
        $dayCareDocuments = $draft->dayCareDocuments()->get();
        $infantDocuments = $draft->infantDocuments()->get();

        actingAs($user)
            ->postJson(route('application-drafts.submit', $draft))
            ->assertOk();

        foreach ($dayCares as $dayCare) {
            foreach ($infants as $infant) {
                $application = $user->applications()
                    ->where('day_care_id', $dayCare->id)
                    ->where('infant_id', $infant->id)
                    ->first();

                assertNotNull($application);

                // make sure the application has the submitted status
                assertEquals(ApplicationStatus::Submitted, $application->status);

                // make sure the applicant is transfered from the draft to the application
                assertEquals(
                    $applicant->only('name', 'phone'),
                    $application->applicant()->first()->only('name', 'phone')
                );

                // make sure that the application's documents are created
                assertEquals(
                    array_values(
                        $documents->map->only('document_id', 'type')->toArray()
                    ),
                    array_values(
                        $application->documents()->get()->map->only('document_id', 'type')->toArray()
                    )
                );

                // make sure that the infant documents are created
                assertEquals(
                    array_values(
                        $infantDocuments
                            ->filter(fn ($infantDocument) => $infantDocument['infant_id'] == $infant->id)
                            ->map
                            ->only('document_id', 'type')
                            ->toArray()
                    ),
                    array_values(
                        $application->infantDocuments()
                            ->get()
                            ->map
                            ->only('document_id', 'type')
                            ->toArray()
                    )
                );

                // make sure the day care documents are created
                assertEquals(
                    array_values(
                        $dayCareDocuments
                            ->filter(fn ($dayCareDocument) => $dayCareDocument['day_care_id'] === $dayCare->id)
                            ->map
                            ->only('document_id', 'type')
                            ->toArray()
                    ),
                    array_values(
                        $application->dayCareDocuments()
                            ->get()
                            ->map
                            ->only('document_id', 'type')
                            ->toArray()
                    )
                );

                // make sure the contacts are transfered from draft to applications
                assertEquals(
                    $contacts->map->only('name', 'phone')->toArray(),
                    $application->contacts()->get()->map->only('name', 'phone')->toArray()
                );
            }
        }

        // make sure that all the draft's documents are deleted
        foreach ($documents as $document) {
            assertModelMissing($document);
        }

        foreach ($dayCareDocuments as $dayCareDocument) {
            assertModelMissing($dayCareDocument);
        }

        foreach ($infantDocuments as $infantDocument) {
            assertModelMissing($infantDocument);
        }

        // make sure the draft's contacts pivots are deleted
        assertTrue(
            ApplicationDraftContact::where('application_draft_id', $draft->id)->doesntExist()
        );

        // make sure that the draft itself is deleted
        assertModelMissing($draft);
    });

    test("users can't submit another user's draft", function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->create();

        actingAs($user)
            ->postJson(route('application-drafts.submit', $draft))
            ->assertForbidden();
    });
});
