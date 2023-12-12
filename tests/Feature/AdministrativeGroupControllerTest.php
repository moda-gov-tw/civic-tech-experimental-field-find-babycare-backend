<?php

use App\Enums\AdministrativeGroupMemberRole;
use App\Models\AdministrativeGroup;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function PHPUnit\Framework\assertTrue;

describe('index', function () {
  test('super users and can see all groups', function () {
    $groups = AdministrativeGroup::factory()->count(2)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->getJson(route('administrative-groups.index'))
      ->assertOk()
      ->assertJson([
        'data' => $groups->map->only('id')->toArray()
      ]);
  });

  test("members can see their groups", function () {
    AdministrativeGroup::factory()->count(2)->create();

    $groups = AdministrativeGroup::factory()->count(2)->create();

    $member = User::factory()->hasAttached($groups, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($member)
      ->getJson(route('administrative-groups.index'))
      ->assertOk()
      ->assertJson([
        'data' => $groups->map->only('id')->toArray()
      ]);
  });
});

describe('store', function () {
  test('super users can create groups', function () {
    $superUser = User::factory()->superUser()->create();;

    $name = 'Group name';

    actingAs($superUser)
      ->postJson(route('administrative-groups.store'), [
        'name' => $name
      ])
      ->assertCreated()
      ->assertJsonPath('data.name', $name);

    assertTrue(AdministrativeGroup::where('name', $name)->exists());
  });

  test("users can't create groups", function () {
    $user = User::factory()->create();

    $name = 'Group name';

    actingAs($user)
      ->postJson(route('administrative-groups.store'), [
        'name' => $name
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue(AdministrativeGroup::where('name', $name)->doesntExist());
  });
});

describe('show', function () {
  test('super users can see group', function () {
    $group = AdministrativeGroup::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->getJson(route('administrative-groups.show', $group))
      ->assertOk()
      ->assertJsonPath('data.id', $group->id);
  });

  test('members can see group', function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($member)
      ->getJson(route('administrative-groups.show', $group))
      ->assertOk()
      ->assertJsonPath('data.id', $group->id);
  });

  test("users can't see group", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('administrative-groups.show', $group))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('update', function () {
  test('super users can update group', function () {
    $group = AdministrativeGroup::factory()->create([
      'name' => 'Original group name'
    ]);

    $superUser = User::factory()->superUser()->create();

    $name = 'New group name';

    actingAs($superUser)
      ->patchJson(route('administrative-groups.update', $group), [
        'name' => $name
      ])
      ->assertOk()
      ->assertJsonPath('data.name', $name);

    assertTrue(AdministrativeGroup::where('name', $name)->exists());
  });

  test('administrators can update group', function () {
    $group = AdministrativeGroup::factory()->create([
      'name' => 'Original group name'
    ]);

    $user = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    $name = 'New group name';

    actingAs($user)
      ->patchJson(route('administrative-groups.update', $group), [
        'name' => 'New group name'
      ])
      ->assertOk()
      ->assertJsonPath('data.name', $name);

    assertTrue(AdministrativeGroup::where('name', $name)->exists());
  });

  test("contributors can't update group", function () {
    $group = AdministrativeGroup::factory()->create([
      'name' => 'Original group name'
    ]);

    $contributor = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $name = 'New group name';

    actingAs($contributor)
      ->patchJson(route('administrative-groups.update', $group), [
        'name' => $name
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue(AdministrativeGroup::where('name', $name)->doesntExist());
  });

  test("users can't update group", function () {
    $group = AdministrativeGroup::factory()->create([
      'name' => 'Original group name'
    ]);

    $user = User::factory()->create();

    $name = 'New group name';

    actingAs($user)
      ->patchJson(route('administrative-groups.update', $group), [
        'name' => $name
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue(AdministrativeGroup::where('name', $name)->doesntExist());
  });
});

describe('destroy', function () {
  test('super users can delete groups', function () {
    $group = AdministrativeGroup::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->deleteJson(route('administrative-groups.destroy', $group))
      ->assertNoContent();

    assertModelMissing($group);
  });

  test("members can't delete groups", function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    actingAs($member)
      ->deleteJson(route('administrative-groups.destroy', $group))
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertModelExists($group);
  });

  test("users can't delete groups", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    actingAs($user)
      ->deleteJson(route('administrative-groups.destroy', $group))
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertModelExists($group);
  });
});
