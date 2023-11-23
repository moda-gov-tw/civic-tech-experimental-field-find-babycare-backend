<?php

use App\Enums\ApplicationStatus;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDayCareDocument;
use App\Models\ApplicationDraftDocument;
use App\Models\ApplicationDraftInfantDocument;
use App\Models\DayCare;
use App\Models\Infant;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

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
            ->postJson(route('application-drafts.store'), [
                'applicant' => [
                    'relationship_with_infant' => 'Parent',
                    'name' => 'Applicant name',
                    'phone' => '123456789',
                    'is_living_in_the_same_household' => true,
                    'address' => [
                        'city' => 'City',
                        'district' => 'District',
                        'street' => 'Street'
                    ]
                ]
            ])
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
    test('users can update one of their application drafts', function () {
        $user = User::factory()->create();

        $draft = ApplicationDraft::factory()->for($user)->create();;

        actingAs($user)
            ->patchJson(route('application-drafts.update', $draft), [
                'applicant' => [
                    'relationship_with_infant' => 'Parent',
                    'name' => 'Applicant name',
                    'phone' => '123456789',
                    'is_living_in_the_same_household' => true,
                    'address' => [
                        'city' => 'City',
                        'district' => 'District',
                        'street' => 'Street'
                    ]
                ]
            ])
            ->assertOk();
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

        $draft = ApplicationDraft::factory()
            ->for($user)
            ->hasAttached($dayCares)
            ->hasAttached($infants)
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
