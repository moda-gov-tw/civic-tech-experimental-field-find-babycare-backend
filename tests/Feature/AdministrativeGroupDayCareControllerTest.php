<?php

use App\Enums\AdministrativeGroupMemberRole;
use App\Models\AdministrativeGroup;
use App\Models\DayCare;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertTrue;

describe('index', function () {
  test("super users can see all day cares", function () {
    $group = AdministrativeGroup::factory()->create();

    $dayCares = DayCare::factory()->hasAttached($group)->count(2)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->getJson(route('administrative-groups.day-cares.index', $group))
      ->assertOk()
      ->assertJson([
        'data' => $dayCares->map->only('id')->toArray()
      ]);
  });

  test("members can see day cares", function () {
    $group = AdministrativeGroup::factory()->create();

    $dayCares = DayCare::factory()->hasAttached($group)->count(2)->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($member)
      ->getJson(route('administrative-groups.day-cares.index', $group))
      ->assertOk()
      ->assertJson([
        'data' => $dayCares->map->only('id')->toArray()
      ]);
  });

  test("users can't see day cares", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('administrative-groups.day-cares.index', $group))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('store', function () {
  test("super users can add day cares", function () {
    $group = AdministrativeGroup::factory()->create();

    $dayCare = DayCare::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->postJson(route('administrative-groups.day-cares.store', $group), [
        'day_care_id' => $dayCare->id
      ])
      ->assertCreated()
      ->assertJsonPath('data.id', $dayCare->id);

    assertTrue($group->dayCares()->where('id', $dayCare->id)->exists());
  });

  test("members can't add day cares", function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    actingAs($member)
      ->postJson(route('administrative-groups.day-cares.store', $group))
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue($group->dayCares()->doesntExist());
  });

  test("users can't add day cares", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    actingAs($user)
      ->postJson(route('administrative-groups.day-cares.store', $group))
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue($group->dayCares()->doesntExist());
  });
});

describe('show', function () {
  test("super users can see day care", function () {
    $group = AdministrativeGroup::factory()->create();

    $dayCare = DayCare::factory()->hasAttached($group)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->getJson(route('administrative-groups.day-cares.show', [$group, $dayCare]))
      ->assertOk()
      ->assertJsonPath('data.id', $dayCare->id);
  });

  test("members can see day care", function () {
    $group = AdministrativeGroup::factory()->create();

    $dayCare = DayCare::factory()->hasAttached($group)->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($member)
      ->getJson(route('administrative-groups.day-cares.show', [$group, $dayCare]))
      ->assertOk()
      ->assertJsonPath('data.id', $dayCare->id);
  });

  test("users can't see day care", function () {
    $group = AdministrativeGroup::factory()->create();

    $dayCare = DayCare::factory()->hasAttached($group)->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('administrative-groups.day-cares.show', [$group, $dayCare]))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('destroy', function () {
  test("super users can remove day care", function () {
    $group = AdministrativeGroup::factory()->create();

    $dayCare = DayCare::factory()->hasAttached($group)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->deleteJson(route('administrative-groups.day-cares.destroy', [$group, $dayCare]))
      ->assertNoContent();

    assertTrue($group->dayCares()->where('id', $dayCare->id)->doesntExist());
  });

  test("members can't remove day care", function () {
    $group = AdministrativeGroup::factory()->create();

    $dayCare = DayCare::factory()->hasAttached($group)->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    actingAs($member)
      ->deleteJson(route('administrative-groups.day-cares.destroy', [$group, $dayCare]))
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue($group->dayCares()->where('id', $dayCare->id)->exists());
  });

  test("users can't remove day care", function () {
    $group = AdministrativeGroup::factory()->create();

    $dayCare = DayCare::factory()->hasAttached($group)->create();

    $user = User::factory()->create();

    actingAs($user)
      ->deleteJson(route('administrative-groups.day-cares.destroy', [$group, $dayCare]))
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue($group->dayCares()->where('id', $dayCare->id)->exists());
  });
});
