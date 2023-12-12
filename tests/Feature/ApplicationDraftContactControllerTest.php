<?php

use App\Models\ApplicationDraft;
use App\Models\Contact;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

describe('index', function () {
  test("user can view their application draft's contacts", function () {
    $user = User::factory()->create();

    $contacts = Contact::factory()->count(2)->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($contacts)->create();

    actingAs($user)
      ->getJson(route('application-drafts.contacts.index', $draft))
      ->assertOk()
      ->assertJson([
        'data' => $contacts->only('id')->toArray()
      ]);
  });

  test("user cannot view other user's application draft's contacts", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->hasContacts(2)->create();

    actingAs($user)
      ->getJson(route('application-drafts.contacts.index', $draft))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('store', function () {
  test("user can add contacts to their application draft", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->create();

    $name = 'Name';

    actingAs($user)
      ->postJson(route('application-drafts.contacts.store', $draft), [
        'relationship_with_infant' => 'Mother',
        'name' => $name,
        'phone' => '1234567890',
        'is_living_in_the_same_household' => true,
        'address' => [
          'city' => 'City',
          'district' => 'District',
          'street' => 'Street',
        ],
      ])
      ->assertCreated()
      ->assertJsonPath('data.name', $name);

    assertTrue($draft->contacts()->where('name', $name)->exists());
  });

  test("user cannot add contacts to other user's application draft", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->create();

    actingAs($user)
      ->postJson(route('application-drafts.contacts.store', $draft), [])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue($draft->contacts()->doesntExist());
  });
});

describe('show', function () {
  test("user can view their application draft's contacts", function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($contact)->create();

    actingAs($user)
      ->getJson(route('application-drafts.contacts.show', [$draft, $contact]))
      ->assertOk()
      ->assertJsonPath('data.id', $contact->id);
  });

  test("user cannot view other user's application draft's contacts", function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    $draft = ApplicationDraft::factory()->hasAttached($contact)->create();

    actingAs($user)
      ->getJson(route('application-drafts.contacts.show', [$draft, $contact]))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('update', function () {
  test("user can update their application draft's contacts", function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($contact)->create();

    $newName = 'New name';

    actingAs($user)
      ->patchJson(route('application-drafts.contacts.update', [$draft, $contact]), [
        'name' => $newName,
      ])
      ->assertOk()
      ->assertJsonPath('data.name', $newName);

    assertEquals($newName, $contact->refresh()->name);
  });

  test("user cannot update other user's application draft's contacts", function () {
    $user = User::factory()->create();

    $originalName = 'Original name';

    $contact = Contact::factory()->create([
      'name' => $originalName,
    ]);

    $draft = ApplicationDraft::factory()->hasAttached($contact)->create();

    actingAs($user)
      ->patchJson(route('application-drafts.contacts.update', [$draft, $contact]), [
        'name' => 'New name',
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertEquals($originalName, $contact->refresh()->name);
  });
});

describe('destroy', function () {
  test("user can delete their application draft's contacts", function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($contact)->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.contacts.destroy', [$draft, $contact]))
      ->assertNoContent();

    assertModelMissing($contact);
  });

  test("user cannot delete other user's application draft's contacts", function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    $draft = ApplicationDraft::factory()->hasAttached($contact)->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.contacts.destroy', [$draft, $contact]))
      ->assertForbidden();

    assertModelExists($contact);
  });
});
