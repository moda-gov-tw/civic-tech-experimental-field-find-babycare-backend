<?php

use App\Enums\AdministrativeGroupMemberRole;
use App\Models\AdministrativeGroup;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertEquals;

describe('index', function () {
  test('super users and can see all groups', function () {
    $groups = AdministrativeGroup::factory()->count(2)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    getJson(route('administrative-groups.index'))
      ->assertOk()
      ->assertJson([
        'data' => $groups->map->only('id', 'name')->toArray()
      ]);
  });

  test("members can see their groups", function () {
    AdministrativeGroup::factory()->count(2)->create();

    $groups = AdministrativeGroup::factory()->count(2)->create();

    $member = User::factory()->hasAttached($groups, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($member);

    getJson(route('administrative-groups.index'))
      ->assertOk()
      ->assertJson([
        'data' => $groups->map->only('id', 'name')->toArray()
      ]);
  });
});

describe('store', function () {
  test('super users can create groups', function () {
    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    postJson(route('administrative-groups.store'), [
      'name' => 'Group name'
    ])->assertCreated();

    assertEquals('Group name', AdministrativeGroup::select('name')->first()->name);
  });

  test("users can't create groups", function () {
    $user = User::factory()->create();

    actingAs($user);

    postJson(route('administrative-groups.store'))->assertForbidden();
  });
});

describe('show', function () {
  test('super users can see group', function () {
    $group = AdministrativeGroup::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    getJson(route('administrative-groups.show', $group))
      ->assertOk()
      ->assertJson([
        'data' => ['name' => $group->name]
      ]);
  });

  test('members can see group', function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($member);

    getJson(route('administrative-groups.show', $group))->assertOk();
  });

  test("users can't see group", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    actingAs($user);

    getJson(route('administrative-groups.show', $group))->assertForbidden();
  });
});

describe('update', function () {
  test('super users can update group', function () {
    $group = AdministrativeGroup::factory()->create([
      'name' => 'Original group name'
    ]);

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    patchJson(route('administrative-groups.update', $group), [
      'name' => 'New group name'
    ])->assertOk();

    assertEquals('New group name', AdministrativeGroup::select('name')->first()->name);
  });

  test('administrators can update group', function () {
    $group = AdministrativeGroup::factory()->create([
      'name' => 'Original group name'
    ]);

    $user = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    actingAs($user);

    patchJson(route('administrative-groups.update', $group), [
      'name' => 'New group name'
    ])->assertOk();

    assertEquals('New group name', AdministrativeGroup::select('name')->first()->name);
  });

  test("contributors can't update group", function () {
    $group = AdministrativeGroup::factory()->create();

    $contributor = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($contributor);

    patchJson(route('administrative-groups.update', $group))->assertForbidden();
  });

  test("users can't update group", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    actingAs($user);

    patchJson(route('administrative-groups.update', $group))->assertForbidden();
  });
});

describe('destroy', function () {
  test('super users can delete groups', function () {
    $group = AdministrativeGroup::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    deleteJson(route('administrative-groups.destroy', $group))->assertOk();

    assertModelMissing($group);
  });

  test("members can't delete groups", function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    actingAs($member);

    deleteJson(route('administrative-groups.destroy', $group))->assertForbidden();
  });

  test("users can't delete groups", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    actingAs($user);

    deleteJson(route('administrative-groups.destroy', $group))->assertForbidden();
  });
});
