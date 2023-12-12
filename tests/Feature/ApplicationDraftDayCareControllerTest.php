
<?php

use App\Enums\DayCareType;
use App\Models\ApplicationDraft;
use App\Models\DayCare;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertTrue;

describe('index', function () {
  test('application draft owner can list day cares', function () {
    $user = User::factory()->create();

    $dayCares = DayCare::factory()->count(2)->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($dayCares)->create();

    actingAs($user)
      ->getJson(route('application-drafts.day-cares.index', $draft))
      ->assertOk()
      ->assertJson([
        'data' => $dayCares->only('id')->toArray()
      ]);
  });

  test("user can't list day cares for application draft that doesn't belong to them", function () {
    $user = User::factory()->create();

    $dayCares = DayCare::factory()->count(2)->create();

    $draft = ApplicationDraft::factory()->hasAttached($dayCares)->create();

    actingAs($user)
      ->getJson(route('application-drafts.day-cares.index', $draft))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('store', function () {
  test('application draft owner can add day care', function () {
    $user = User::factory()->create();

    $dayCare = DayCare::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->create();

    actingAs($user)
      ->postJson(route('application-drafts.day-cares.store', $draft), [
        'day_care_id' => $dayCare->id
      ])
      ->assertCreated()
      ->assertJsonPath('data.id', $dayCare->id);

    assertTrue(
      $draft
        ->dayCares()
        ->where('id', $dayCare->id)
        ->exists()
    );
  });

  test("user can't add day care for application draft that doesn't belong to them", function () {
    $user = User::factory()->create();

    $dayCare = DayCare::factory()->create();

    $draft = ApplicationDraft::factory()->create();

    actingAs($user)
      ->postJson(route('application-drafts.day-cares.store', $draft), [
        'day_care_id' => $dayCare->id
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue(
      $draft
        ->dayCares()
        ->where('id', $dayCare->id)
        ->doesntExist()
    );
  });

  test("only public day cares can be added to application draft", function () {
    $user = User::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->create();

    $types = array_diff(DayCareType::values(), [DayCareType::Public->value]);

    foreach ($types as $type) {
      $dayCare = DayCare::factory()->create([
        'type' => $type
      ]);

      actingAs($user)
        ->postJson(route('application-drafts.day-cares.store', $draft), [
          'day_care_id' => $dayCare->id
        ])
        ->assertUnprocessable()
        ->assertJsonMissingPath('data');

      assertTrue(
        $draft
          ->dayCares()
          ->where('id', $dayCare->id)
          ->doesntExist()
      );
    }
  });
});

describe('show', function () {
  test('application draft owner can see day care', function () {
    $user = User::factory()->create();

    $dayCare = DayCare::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($dayCare)->create();

    actingAs($user)
      ->getJson(route('application-drafts.day-cares.show', [$draft, $dayCare]))
      ->assertOk()
      ->assertJsonPath('data.id', $dayCare->id);
  });

  test("user can't see day care for application draft that doesn't belong to them", function () {
    $user = User::factory()->create();

    $dayCare = DayCare::factory()->create();

    $draft = ApplicationDraft::factory()->hasAttached($dayCare)->create();

    actingAs($user)
      ->getJson(route('application-drafts.day-cares.show', [$draft, $dayCare]))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('update', function () {
  test('application draft owner can update day care', function () {
    $user = User::factory()->create();

    $dayCare = DayCare::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($dayCare)->create();

    actingAs($user)
      ->patchJson(route('application-drafts.day-cares.update', [$draft, $dayCare]), [])
      ->assertOk()
      ->assertJsonPath('data.id', $dayCare->id);
  });

  test("user can't update day care for application draft that doesn't belong to them", function () {
    $user = User::factory()->create();

    $dayCare = DayCare::factory()->create();

    $draft = ApplicationDraft::factory()->hasAttached($dayCare)->create();

    actingAs($user)
      ->patchJson(route('application-drafts.day-cares.update', [$draft, $dayCare]), [])
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('delete', function () {
  test('application draft owner can remove day care', function () {
    $user = User::factory()->create();

    $dayCare = DayCare::factory()->create();

    $draft = ApplicationDraft::factory()->for($user)->hasAttached($dayCare)->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.day-cares.destroy', [$draft, $dayCare]))
      ->assertNoContent();

    assertTrue(
      $draft
        ->dayCares()
        ->where('id', $dayCare->id)
        ->doesntExist()
    );
  });

  test("user can't remove day care for application draft that doesn't belong to them", function () {
    $user = User::factory()->create();

    $dayCare = DayCare::factory()->create();

    $draft = ApplicationDraft::factory()->hasAttached($dayCare)->create();

    actingAs($user)
      ->deleteJson(route('application-drafts.day-cares.destroy', [$draft, $dayCare]))
      ->assertForbidden();

    assertTrue(
      $draft
        ->dayCares()
        ->where('id', $dayCare->id)
        ->exists()
    );
  });
});
