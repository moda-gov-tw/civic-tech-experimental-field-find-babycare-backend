<?php

use App\Enums\AdministrativeGroupMemberRole;
use App\Models\AdministrativeGroup;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

describe('index', function () {
  test('super users can get members', function () {
    $group = AdministrativeGroup::factory()->create();

    User::factory()->count(2)->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->getJson(route('administrative-groups.members.index', $group))
      ->assertOk()
      ->assertJson([
        'data' => $group->members()->get()->only('id')->toArray()
      ]);
  });

  test("members can get members", function () {
    $group = AdministrativeGroup::factory()->create();

    User::factory()->count(2)->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $contributor = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($contributor)
      ->getJson(route('administrative-groups.members.index', $group))
      ->assertOk()
      ->assertJson([
        'data' => $group->members()->get()->only('id')->toArray()
      ]);
  });

  test("users can't get members", function () {
    $group = AdministrativeGroup::factory()->create();

    User::factory()->count(2)->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('administrative-groups.members.index', $group))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('store', function () {
  test("super users can add members", function () {
    $group = AdministrativeGroup::factory()->create();

    $userToAdd = User::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->postJson(route('administrative-groups.members.store', $group), [
        'email' => $userToAdd->email,
        'role' => AdministrativeGroupMemberRole::Contributor
      ])
      ->assertCreated()
      ->assertJsonPath('data.id', $userToAdd->id);

    assertTrue(
      $group->members()
        ->where('id', $userToAdd->id)
        ->wherePivot('role', AdministrativeGroupMemberRole::Contributor)
        ->exists()
    );
  });

  test('administrators can add members', function () {
    $group = AdministrativeGroup::factory()->create();

    $userToAdd = User::factory()->create();

    $administrator = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    actingAs($administrator)
      ->postJson(route('administrative-groups.members.store', $group), [
        'email' => $userToAdd->email,
        'role' => AdministrativeGroupMemberRole::Contributor
      ])
      ->assertCreated()
      ->assertJsonPath('data.id', $userToAdd->id);

    assertTrue(
      $group->members()
        ->where('id', $userToAdd->id)
        ->wherePivot('role', AdministrativeGroupMemberRole::Contributor)
        ->exists()
    );
  });

  test("contributors can't add members", function () {
    $group = AdministrativeGroup::factory()->create();

    $userToAdd = User::factory()->create();

    $contributor = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($contributor)
      ->postJson(route('administrative-groups.members.store', $group), [
        'email' => $userToAdd->email,
        'role' => AdministrativeGroupMemberRole::Contributor
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue(
      $group->members()
        ->where('id', $userToAdd->id)
        ->doesntExist()
    );
  });

  test("users can't add members", function () {
    $group = AdministrativeGroup::factory()->create();

    $userToAdd = User::factory()->create();

    $user = User::factory()->create();

    actingAs($user)
      ->postJson(route('administrative-groups.members.store', $group), [
        'email' => $userToAdd->email,
        'role' => AdministrativeGroupMemberRole::Contributor
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue(
      $group->members()
        ->where('id', $userToAdd->id)
        ->doesntExist()
    );
  });

  test("can't add member with a non existing email", function () {
    $group = AdministrativeGroup::factory()->create();

    $superUser = User::factory()->superUser()->create();

    $email = 'nonexisting@email.com';

    actingAs($superUser)
      ->postJson(route('administrative-groups.members.store', $group), [
        'email' => $email,
        'role' => AdministrativeGroupMemberRole::Contributor
      ])
      ->assertUnprocessable()
      ->assertJsonMissingPath('data');

    assertTrue(
      $group->members()
        ->where('email', $email)
        ->doesntExist()
    );
  });

  test("can't create member with an invalid role", function () {
    $group = AdministrativeGroup::factory()->create();

    $userToInvite = User::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->postJson(route('administrative-groups.members.store', $group), [
        'email' => $userToInvite->email,
        'role' => 'invalid-role'
      ])
      ->assertUnprocessable()
      ->assertJsonMissingPath('data');

    assertTrue(
      $group->members()
        ->where('id', $userToInvite->id)
        ->doesntExist()
    );
  });
});

describe('show', function () {
  test('super users can get member', function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->getJson(route('administrative-groups.members.show', [$group, $member]))
      ->assertOk()
      ->assertJsonPath('data.id', $member->id);
  });

  test('members can get member', function () {
    $group = AdministrativeGroup::factory()->create();

    $memberToShow = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($member)
      ->getJson(route('administrative-groups.members.show', [$group, $memberToShow]))
      ->assertOk()
      ->assertJsonPath('data.id', $memberToShow->id);
  });

  test("users can't get members", function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('administrative-groups.members.show', [$group, $member]))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('update', function () {
  test("super users can update members", function () {
    $group = AdministrativeGroup::factory()->create();

    $superUser = User::factory()->superUser()->create();

    $memberToUpdate = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $role = AdministrativeGroupMemberRole::Administrator;

    actingAs($superUser)
      ->patchJson(route('administrative-groups.members.update', [$group, $memberToUpdate]), [
        'role' => $role
      ])
      ->assertOk()
      ->assertJsonPath('data.role', $role->value);

    assertEquals(
      $role,
      $group->members()
        ->where('id', $memberToUpdate->id)
        ->first()->pivot->role
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

    $role = AdministrativeGroupMemberRole::Administrator;

    actingAs($administrator)
      ->patchJson(route('administrative-groups.members.update', [$group, $memberToUpdate]), [
        'role' => AdministrativeGroupMemberRole::Administrator
      ])
      ->assertOk()
      ->assertJsonPath('data.role', $role->value);

    assertEquals(
      $role,
      $group->members()
        ->where('id', $memberToUpdate->id)
        ->first()->pivot->role
    );
  });

  test("contributors can't update members", function () {
    $group = AdministrativeGroup::factory()->create();

    $contributor = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $role = AdministrativeGroupMemberRole::Contributor;

    $memberToUpdate = User::factory()->hasAttached($group, [
      'role' => $role
    ])->create();

    actingAs($contributor)
      ->patchJson(route('administrative-groups.members.update', [$group, $memberToUpdate]), [
        'role' => AdministrativeGroupMemberRole::Administrator
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertEquals(
      $role,
      $group->members()
        ->where('id', $memberToUpdate->id)
        ->first()->pivot->role
    );
  });

  test("users can't update members", function () {
    $group = AdministrativeGroup::factory()->create();

    $user = User::factory()->create();

    $role = AdministrativeGroupMemberRole::Contributor;

    $memberToUpdate = User::factory()->hasAttached($group, [
      'role' => $role
    ])->create();

    actingAs($user)
      ->patchJson(route('administrative-groups.members.update', [$group, $memberToUpdate]), [
        'role' => AdministrativeGroupMemberRole::Administrator
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertEquals(
      $role,
      $group->members()
        ->where('id', $memberToUpdate->id)
        ->first()->pivot->role
    );
  });
});

describe('destroy', function () {
  test('super users can delete members', function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->deleteJson(route('administrative-groups.members.destroy', [$group, $member]))
      ->assertNoContent();

    assertTrue($group->members()->where('id', $member->id)->doesntExist());
  });

  test('administrators can delete members', function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $administrator = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Administrator
    ])->create();

    actingAs($administrator)
      ->deleteJson(route('administrative-groups.members.destroy', [$group, $member]))
      ->assertNoContent();

    assertTrue($group->members()->where('id', $member->id)->doesntExist());
  });

  test("contributors can't delete members", function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $contributor = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    actingAs($contributor)
      ->deleteJson(route('administrative-groups.members.destroy', [$group, $member]))
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue($group->members()->where('id', $member->id)->exists());
  });

  test("users can't delete members", function () {
    $group = AdministrativeGroup::factory()->create();

    $member = User::factory()->hasAttached($group, [
      'role' => AdministrativeGroupMemberRole::Contributor
    ])->create();

    $user = User::factory()->create();

    actingAs($user)
      ->deleteJson(route('administrative-groups.members.destroy', [$group, $member]))
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue($group->members()->where('id', $member->id)->exists());
  });
});
