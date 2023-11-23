<?php

use App\Enums\ApplicationDayCareDocumentType;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDayCareDocument;
use App\Models\DayCare;
use App\Models\User;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelMissing;
use function PHPUnit\Framework\assertFalse;

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
      ->assertCreated();
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
      ->assertForbidden();
  });

  test("documents should be for a draft's infant", function () {
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
      ->assertUnprocessable();
  });
});

describe('show', function () {
  test("application draft's owner can see documents", function () {
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

  test("users can't see documents", function () {
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
  test("owner can delete documents", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()
      ->for($user)
      ->create();

    $document = ApplicationDraftDayCareDocument::factory()
      ->for($draft)
      ->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.day-care-documents.destroy', [$draft, $document]))
      ->assertOk();

    assertModelMissing($document);
    assertFalse(Storage::exists($document->path));
  });

  test("users can't delete documents", function () {
    $draft = ApplicationDraft::factory()->create();

    $document = ApplicationDraftDayCareDocument::factory()
      ->for($draft)
      ->create();

    $user = User::factory()->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.day-care-documents.destroy', [$draft, $document]))
      ->assertForbidden();
  });
});
