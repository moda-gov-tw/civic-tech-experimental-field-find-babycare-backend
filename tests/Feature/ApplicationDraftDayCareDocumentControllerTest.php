<?php

use App\Enums\ApplicationDayCareDocumentType;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDayCareDocument;
use App\Models\DayCare;
use App\Models\User;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

describe('store', function () {
  test("owner can create documents", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->create();

    $dayCare = DayCare::factory()->hasAttached($draft)->create();

    $file = UploadedFile::fake()->create('document.jpg', 2048);

    actingAs($user)
      ->postJson(route('application-drafts.day-care-documents.store', $draft), [
        'day_care_id' => $dayCare->id,
        'type' => ApplicationDayCareDocumentType::ReservedSpotQualificationProof,
        'file' => $file
      ])
      ->assertCreated()
      ->assertJson([
        'data' => [
          'day_care_id' => $dayCare->id,
          'type' => ApplicationDayCareDocumentType::ReservedSpotQualificationProof->value,
        ]
      ]);

    assertTrue(
      $draft
        ->dayCareDocuments()
        ->where('day_care_id', $dayCare->id)
        ->where('type', ApplicationDayCareDocumentType::ReservedSpotQualificationProof)
        ->exists()
    );
  });

  test("users can't create documents", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->create();

    $dayCare = DayCare::factory()->hasAttached($draft)->create();

    $file = UploadedFile::fake()->create('document.jpg', 2048);

    actingAs($user)
      ->postJson(route('application-drafts.day-care-documents.store', $draft), [
        'day_care_id' => $dayCare->id,
        'type' => ApplicationDayCareDocumentType::ReservedSpotQualificationProof,
        'file' => $file
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue(
      $draft
        ->dayCareDocuments()
        ->where('day_care_id', $dayCare->id)
        ->where('type', ApplicationDayCareDocumentType::ReservedSpotQualificationProof)
        ->doesntExist()
    );
  });

  test("documents should be for a draft's day care", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->create();

    $dayCare = DayCare::factory()->create();

    $file = UploadedFile::fake()->create('document.jpg', 2048);

    actingAs($user)
      ->postJson(route('application-drafts.day-care-documents.store', $draft), [
        'day_care_id' => $dayCare->id,
        'type' => ApplicationDayCareDocumentType::ReservedSpotQualificationProof,
        'file' => $file
      ])
      ->assertUnprocessable()
      ->assertJsonMissingPath('data');

    assertTrue(
      $draft
        ->dayCareDocuments()
        ->where('day_care_id', $dayCare->id)
        ->where('type', ApplicationDayCareDocumentType::ReservedSpotQualificationProof)
        ->doesntExist()
    );
  });
});

describe('show', function () {
  test("application draft's owner can see document", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()
      ->for($user)
      ->create();

    $document = ApplicationDraftDayCareDocument::factory()
      ->for($draft)
      ->create();

    actingAs($user)
      ->getJson(route('application-drafts.day-care-documents.show', [$draft, $document]))
      ->assertOk()
      ->assertHeader('Content-Type', 'image/jpeg');
  });

  test("users can't see document", function () {
    $draft = ApplicationDraft::factory()->create();

    $document = ApplicationDraftDayCareDocument::factory()
      ->for($draft)
      ->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('application-drafts.day-care-documents.show', [$draft, $document]))
      ->assertForbidden();
  });
});

describe('delete', function () {
  test("owner can delete document", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()
      ->for($user)
      ->create();

    $document = ApplicationDraftDayCareDocument::factory()
      ->for($draft)
      ->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.day-care-documents.destroy', [$draft, $document]))
      ->assertNoContent();

    assertModelMissing($document);
    assertFalse(Storage::exists($document->path));
  });

  test("users can't delete document", function () {
    $draft = ApplicationDraft::factory()->create();

    $document = ApplicationDraftDayCareDocument::factory()
      ->for($draft)
      ->create();

    $user = User::factory()->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.day-care-documents.destroy', [$draft, $document]))
      ->assertForbidden();

    assertModelExists($document);
    assertTrue(Storage::exists($document->path));
  });
});
