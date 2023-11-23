<?php

use App\Enums\ApplicationInfantDocumentType;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftInfantDocument;
use App\Models\Infant;
use App\Models\User;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelMissing;
use function PHPUnit\Framework\assertFalse;

describe('store', function () {
  test("owner can create documents", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->create();

    $infant = Infant::factory()->hasAttached($draft)->create();

    $file = UploadedFile::fake()->create('document.jpg', 2048);

    actingAs($user)
      ->postJson(route('application-drafts.infant-documents.store', $draft), [
        'infant_id' => $infant->id,
        'type' => ApplicationInfantDocumentType::SpecialMedicalConditionDiagnosis,
        'file' => $file
      ])
      ->assertCreated();
  });

  test("users can't create documents", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->create();

    $infant = Infant::factory()->hasAttached($draft)->create();

    $file = UploadedFile::fake()->create('document.jpg', 2048);

    actingAs($user)
      ->postJson(route('application-drafts.infant-documents.store', $draft), [
        'infant_id' => $infant->id,
        'type' => ApplicationInfantDocumentType::SpecialMedicalConditionDiagnosis,
        'file' => $file
      ])
      ->assertForbidden();
  });

  test("documents should be for a draft's infant", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->create();

    $infant = Infant::factory()->create();

    $file = UploadedFile::fake()->create('document.jpg', 2048);

    actingAs($user)
      ->postJson(route('application-drafts.infant-documents.store', $draft), [
        'infant_id' => $infant->id,
        'type' => ApplicationInfantDocumentType::SpecialMedicalConditionDiagnosis,
        'file' => $file
      ])
      ->assertUnprocessable();
  });
});

describe('show', function () {
  test("owner can see documents", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()
      ->for($user)
      ->create();

    $document = ApplicationDraftInfantDocument::factory()
      ->for($draft)
      ->create();

    actingAs($user)
      ->getJson(route('application-drafts.infant-documents.show', [$draft, $document]))
      ->assertOk()
      ->assertHeader('Content-Type', 'image/jpeg');
  });

  test("users can't see documents", function () {
    $draft = ApplicationDraft::factory()->create();

    $document = ApplicationDraftInfantDocument::factory()
      ->for($draft)
      ->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('application-drafts.infant-documents.show', [$draft, $document]))
      ->assertForbidden();
  });
});

describe('delete', function () {
  test("owner can delete documents", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()
      ->for($user)
      ->create();

    $document = ApplicationDraftInfantDocument::factory()
      ->for($draft)
      ->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.infant-documents.destroy', [$draft, $document]))
      ->assertOk();

    assertModelMissing($document);
    assertFalse(Storage::exists($document->path));
  });

  test("users can't delete documents", function () {
    $draft = ApplicationDraft::factory()->create();

    $document = ApplicationDraftInfantDocument::factory()
      ->for($draft)
      ->create();

    $user = User::factory()->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.infant-documents.destroy', [$draft, $document]))
      ->assertForbidden();
  });
});
