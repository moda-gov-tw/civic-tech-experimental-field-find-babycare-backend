<?php

use App\Enums\AdministrativeGroupMemberRole;
use App\Models\AdministrativeGroup;
use App\Models\AdministrativeGroupMember;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

describe('index', function () {
  test('super users can get members', function () {
    $group = AdministrativeGroup::factory()->create();

    AdministrativeGroupMember::factory()->for($group)->count(2)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    getJson(route('administrative-groups.members.index', $group))
      ->assertOk()
      ->assertJson([
        'data' => $group->members()->get()->only('id', 'name', 'email', 'role')->toArray()
      ]);
  });

  test("members can get members", function () {
    $group = AdministrativeGroup::factory()->create();

    AdministrativeGroupMember::factory()->for($group)->count(2)->create();

    $contributor = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($contributor);

    getJson(route('administrative-groups.members.index', $group))
      ->assertOk()
      ->assertJson([
        'data' => $group->members()->get()->only('id', 'name', 'email', 'role')->toArray()
      ]);
  });

  test("users can't get members", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    actingAs($user);

    getJson(route('administrative-groups.members.index', $group))
      ->assertForbidden();
  });
});

describe('store', function () {
  test("super users can add members", function () {
    $group = AdministrativeGroup::factory()->create();

    $userToAdd = User::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    postJson(route('administrative-groups.members.store', $group), [
      'email' => $userToAdd->email,
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->assertCreated();

    assertNotNull(
      $group->members()
        ->wherePivot('user_id', $userToAdd->id)
        ->wherePivot('role', AdministrativeGroupMemberRole::Contributor)
        ->first()
    );
  });

  test('administrators can add members', function () {
    $group = AdministrativeGroup::factory()->create();

    $userToAdd = User::factory()->create();

    $administrator = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    actingAs($administrator);

    postJson(route('administrative-groups.members.store', $group), [
      'email' => $userToAdd->email,
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->assertCreated();

    assertNotNull(
      $group->members()
        ->wherePivot('user_id', $userToAdd->id)
        ->wherePivot('role', AdministrativeGroupMemberRole::Contributor)
        ->first()
    );
  });

  test("contributors can't add members", function () {
    $group = AdministrativeGroup::factory()->create();

    $contributor = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($contributor);

    postJson(route('administrative-groups.members.store', $group))
      ->assertForbidden();
  });

  test("users can't add members", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    actingAs($user);

    postJson(route('administrative-groups.members.store', $group))
      ->assertForbidden();
  });

  test("can't add member with a non existing email", function () {
    $group = AdministrativeGroup::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    postJson(route('administrative-groups.members.store', $group), [
      'email' => 'nonexisting@email.com',
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->assertUnprocessable();
  });

  test("can't create member with an invalid role", function () {
    $group = AdministrativeGroup::factory()->create();

    $userToInvite = User::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    postJson(route('administrative-groups.members.store', $group), [
      'email' => $userToInvite->email,
      'role' => 'invalid-role'
    ])->assertUnprocessable();
  });
});

describe('show', function () {
  test('super users can get member', function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    getJson(route('administrative-groups.members.show', [$group, $member]))
      ->assertOk()
      ->assertJson(['data' => $member->only('id')]);
  });

  test('members can get member', function () {
    $group = AdministrativeGroup::factory()->create();

    $memberToShow = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($member);

    getJson(route('administrative-groups.members.show', [$group, $memberToShow]))
      ->assertOk()
      ->assertJson(['data' => $memberToShow->only('id')]);
  });

  test("users can't get members", function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $user = User::factory()->create();

    actingAs($user);

    getJson(route('administrative-groups.members.show', [$group, $member]))
      ->assertForbidden();
  });
});

describe('update', function () {
  test("super users can update members", function () {
    $group = AdministrativeGroup::factory()->create();

    $superUser = User::factory()->superUser()->create();

    $memberToUpdate = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($superUser);

    patchJson(route('administrative-groups.members.update', [$group, $memberToUpdate]), [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->assertOk();

    assertEquals(
      AdministrativeGroupMemberRole::Administrator,
      $group->members()->where('id', $memberToUpdate->id)->first()->pivot->role
    );
  });

  test('administrators can update members', function () {
    $group = AdministrativeGroup::factory()->create();

    $administrator = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    $memberToUpdate = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($administrator);

    patchJson(route('administrative-groups.members.update', [$group, $memberToUpdate]), [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->assertOk();

    assertEquals(
      AdministrativeGroupMemberRole::Administrator,
      $group->members()->where('id', $memberToUpdate->id)->first()->pivot->role
    );
  });

  test("contributors can't update members", function () {
    $group = AdministrativeGroup::factory()->create();

    $contributor = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $memberToUpdate = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($contributor);

    patchJson(route('administrative-groups.members.update', [$group, $memberToUpdate]), [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->assertForbidden();
  });

  test("users can't update members", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    $memberToUpdate = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($user);

    patchJson(route('administrative-groups.members.update', [$group, $memberToUpdate]), [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->assertForbidden();
  });
});

describe('destroy', function () {
  test('super users can delete members', function () {
    $group = AdministrativeGroup::factory()->create();

    $member = AdministrativeGroupMember::factory()->for($group)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    deleteJson(route('administrative-groups.members.destroy', [$group, $member->user_id]))
      ->assertOk();

    assertNull($group->members()->where('id', $member->id)->first());
  });

  test('administrators can delete members', function () {
    $group = AdministrativeGroup::factory()->create();

    $member = AdministrativeGroupMember::factory()->for($group)->create();

    $administrator = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    actingAs($administrator);

    deleteJson(route('administrative-groups.members.destroy', [$group, $member->user_id]))
      ->assertOk();

    assertNull($group->members()->where('id', $member->id)->first());
  });

  test("contributors can't delete members", function () {
    $group = AdministrativeGroup::factory()->create();

    $member = AdministrativeGroupMember::factory()->for($group)->create();

    $contributor = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($contributor);

    deleteJson(route('administrative-groups.members.destroy', [$group, $member->user_id]))
      ->assertForbidden();
  });

  test("users can't delete members", function () {
    $group = AdministrativeGroup::factory()->create();

    $member = AdministrativeGroupMember::factory()->for($group)->create();

    $user = User::factory()->create();

    actingAs($user);

    deleteJson(route('administrative-groups.members.destroy', [$group, $member->user_id]))
      ->assertForbidden();
  });
});
