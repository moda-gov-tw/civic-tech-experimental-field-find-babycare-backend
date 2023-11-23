<?php

use App\Enums\InfantStatusType;
use App\Models\ApplicationDraft;
use App\Models\Infant;
use App\Models\User;

use function Pest\Laravel\actingAs;

describe('store', function () {
  test("user can create infants for their application draft", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->create();

    actingAs($user)
      ->postJson(
        route('application-drafts.infants.store', $draft),
        [
          'name' => 'Infant name',
          'id_number' => '123456789',
          'dob' => '2020-01-01',
          'address' => [
            'city' => 'City',
            'district' => 'District',
            'street' => 'Street'
          ],
          'statuses' => [
            InfantStatusType::Aboriginal,
            InfantStatusType::Adopted
          ]
        ]
      )
      ->assertCreated()
      ->assertJson([
        'data' => ['name' => 'Infant name']
      ]);
  });

  test("user can't create infant for application draft that doesn't belong to them", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->create();

    actingAs($user)
      ->postJson(route('application-drafts.infants.store', $draft))
      ->assertForbidden();
  });
});

describe('index', function () {
  test("user can list infants from their application draft", function () {
    $user = User::factory()->create();

    $infants = Infant::factory()->count(2)->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($infants)->create();

    actingAs($user)
      ->getJson(route('application-drafts.infants.index', $draft))
      ->assertOk()
      ->assertJson([
        'data' => $infants->only('id')->toArray()
      ]);
  });

  test("user can't list infants from application draft that doesn't belong to them", function () {
    $user = User::factory()->create();

    $infants = Infant::factory()->count(2)->create();

    $draft = ApplicationDraft::factory()->hasAttached($infants)->create();

    actingAs($user)
      ->getJson(route('application-drafts.infants.index', $draft))
      ->assertForbidden();
  });
});

describe('show', function () {
  test('user can view infant from their application draft', function () {
    $user = User::factory()->create();

    $infant = Infant::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($infant)->create();

    actingAs($user)
      ->getJson(route('application-drafts.infants.show', [$draft, $infant]))
      ->assertOk()
      ->assertJson([
        'data' => $infant->only('id')
      ]);
  });

  test("user can't view infant from application draft that doesn't belong to them", function () {
    $user = User::factory()->create();

    $infant = Infant::factory()->create();

    $draft = ApplicationDraft::factory()->hasAttached($infant)->create();

    actingAs($user)
      ->getJson(route('application-drafts.infants.show', [$draft, $infant]))
      ->assertForbidden();
  });
});

describe('update', function () {
  test('user can update infant from their application draft', function () {
    $user = User::factory()->create();

    $infant = Infant::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($infant)->create();

    actingAs($user)
      ->patchJson(route('application-drafts.infants.update', [$draft, $infant]), [
        'name' => 'New name',
        'address' => [
          'city' => 'New city',
          'district' => 'New district',
          'street' => 'New street'
        ],
        'statuses' => [
          InfantStatusType::Adopted
        ]
      ])
      ->assertOk();
  });

  test("user can't update infant from application draft that doesn't belong to them", function () {
    $user = User::factory()->create();

    $infant = Infant::factory()->create();

    $draft = ApplicationDraft::factory()->hasAttached($infant)->create();

    actingAs($user)
      ->patchJson(route('application-drafts.infants.update', [$draft, $infant]))
      ->assertForbidden();
  });
});

describe('delete', function () {
  test('user can delete infant from their application draft', function () {
    $user = User::factory()->create();

    $infant = Infant::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($infant)->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.infants.destroy', [$draft, $infant]))
      ->assertNoContent();
  });

  test("user can't delete infant from application draft that doesn't belong to them", function () {
    $user = User::factory()->create();

    $infant = Infant::factory()->create();

    $draft = ApplicationDraft::factory()->hasAttached($infant)->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.infants.destroy', [$draft, $infant]))
      ->assertForbidden();
  });
});
