<?php

use App\Enums\ApplicationDocumentType;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelMissing;
use function PHPUnit\Framework\assertFalse;

describe('store', function () {
  test("application's draft owner can create documents", function () {

    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->create();

    $file = UploadedFile::fake()->create('document.jpg', 2048, 'image/jpeg');

    actingAs($user)
      ->postJson(route('application-drafts.documents.store', $draft), [
        'type' => ApplicationDocumentType::HouseholdRegistration,
        'file' => $file
      ])
      ->assertCreated();
  });

  test("users can't create documents", function () {
    $draft = ApplicationDraft::factory()->create();

    $file = UploadedFile::fake()->create('document.jpg', 2048, 'image/jpeg');

    $user = User::factory()->create();

    actingAs($user)
      ->postJson(route('application-drafts.documents.store', $draft), [
        'type' => ApplicationDocumentType::HouseholdRegistration,
        'file' => $file
      ])
      ->assertForbidden();
  });
});

describe('show', function () {
  test("application draft's owner can see documents", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()
      ->for($user)
      ->create();

    $document = ApplicationDraftDocument::factory()
      ->for($draft)
      ->create();

    actingAs($user)
      ->getJson(route('application-drafts.documents.show', [$draft, $document]))
      ->assertOk()
      ->assertHeader('content-type', 'image/jpeg');
  });

  test("users can't see documents", function () {
    $draft = ApplicationDraft::factory()->create();

    $document = ApplicationDraftDocument::factory()
      ->for($draft)
      ->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('application-drafts.documents.show', [$draft, $document]))
      ->assertForbidden();
  });
});

describe('delete', function () {
  test("application draft's owner can delete documents", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()
      ->for($user)
      ->create();

    $document = ApplicationDraftDocument::factory()
      ->for($draft)
      ->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.documents.destroy', [$draft, $document]))
      ->assertOk();

    assertModelMissing($document);
    assertFalse(Storage::exists($document->path));
  });

  test("users can't delete documents", function () {
    $draft = ApplicationDraft::factory()->create();

    $document = ApplicationDraftDocument::factory()
      ->for($draft)
      ->create();

    $user = User::factory()->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.documents.destroy', [$draft, $document]))
      ->assertForbidden();
  });
});
