<?php

use App\Enums\DayCareMemberRole;
use App\Models\DayCare;
use App\Models\DayCareMember;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

describe('index', function () {
  test('super users can get members', function () {
    $dayCare = DayCare::factory()->create();

    $members = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->count(2)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->getJson(route('day-cares.members.index', $dayCare))
      ->assertOk()
      ->assertJson([
        'data' => $members->only('id')->toArray()
      ]);
  });

  test("members can get members", function () {
    $dayCare = DayCare::factory()->create();

    User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->count(2)->create();

    $contributor = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($contributor)
      ->getJson(route('day-cares.members.index', $dayCare))
      ->assertOk()
      ->assertJson([
        'data' => $dayCare->members()->get()->only('id')->toArray()
      ]);
  });

  test("users can't get members", function () {
    $dayCare = DayCare::factory()->create();

    User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->count(2)->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('day-cares.members.index', $dayCare))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('store', function () {
  test("super users can add members", function () {
    $dayCare = DayCare::factory()->create();

    $userToAdd = User::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->postJson(route('day-cares.members.store', $dayCare), [
        'email' => $userToAdd->email,
        'role' => DayCareMemberRole::Contributor
      ])
      ->assertCreated()
      ->assertJsonPath('data.id', $userToAdd->id);

    assertTrue(
      $dayCare->members()
        ->where('id', $userToAdd->id)
        ->wherePivot('role', DayCareMemberRole::Contributor)
        ->exists()
    );
  });

  test('administrators can add members', function () {
    $dayCare = DayCare::factory()->create();

    $userToAdd = User::factory()->create();

    $administrator = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Administrator
    ])->create();

    actingAs($administrator)
      ->postJson(route('day-cares.members.store', $dayCare), [
        'email' => $userToAdd->email,
        'role' => DayCareMemberRole::Contributor
      ])
      ->assertCreated()
      ->assertJsonPath('data.id', $userToAdd->id);

    assertTrue(
      $dayCare->members()
        ->where('id', $userToAdd->id)
        ->wherePivot('role', DayCareMemberRole::Contributor)
        ->exists()
    );
  });

  test("contributors can't add members", function () {
    $dayCare = DayCare::factory()->create();

    $userToAdd = User::factory()->create();

    $contributor = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($contributor)
      ->postJson(route('day-cares.members.store', $dayCare), [
        'email' => $userToAdd->email,
        'role' => DayCareMemberRole::Contributor
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue(
      $dayCare->members()
        ->where('id', $userToAdd->id)
        ->doesntExist()
    );
  });

  test("users can't add members", function () {
    $dayCare = DayCare::factory()->create();

    $userToAdd = User::factory()->create();

    $user = User::factory()->create();

    actingAs($user)
      ->postJson(route('day-cares.members.store', $dayCare), [
        'email' => $userToAdd->email,
        'role' => DayCareMemberRole::Contributor
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertTrue(
      $dayCare->members()
        ->where('id', $userToAdd->id)
        ->doesntExist()
    );
  });

  test("can't add member with a non existing email", function () {
    $dayCare = DayCare::factory()->create();

    $superUser = User::factory()->superUser()->create();

    $email = 'nonexisting@email.com';

    actingAs($superUser)
      ->postJson(route('day-cares.members.store', $dayCare), [
        'email' => $email,
        'role' => DayCareMemberRole::Contributor
      ])
      ->assertUnprocessable()
      ->assertJsonMissingPath('data');

    assertTrue(
      $dayCare->members()
        ->where('email', $email)
        ->doesntExist()
    );
  });

  test("can't create member with an invalid role", function () {
    $dayCare = DayCare::factory()->create();

    $userToInvite = User::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->postJson(route('day-cares.members.store', $dayCare), [
        'email' => $userToInvite->email,
        'role' => 'invalid-role'
      ])
      ->assertUnprocessable()
      ->assertJsonMissingPath('data');

    assertTrue(
      $dayCare->members()
        ->where('id', $userToInvite->id)
        ->doesntExist()
    );
  });
});

describe('show', function () {
  test('super users can get member', function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->getJson(route('day-cares.members.show', [$dayCare, $member]))
      ->assertOk()
      ->assertJsonPath('data.id', $member->id);
  });

  test('members can get member', function () {
    $dayCare = DayCare::factory()->create();

    $memberToShow = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($member)
      ->getJson(route('day-cares.members.show', [$dayCare, $memberToShow]))
      ->assertOk()
      ->assertJsonPath('data.id', $memberToShow->id);
  });

  test("users can't get members", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('day-cares.members.show', [$dayCare, $member]))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('update', function () {
  test("super users can update members", function () {
    $dayCare = DayCare::factory()->create();

    $superUser = User::factory()->superUser()->create();

    $memberToUpdate = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $role = DayCareMemberRole::Administrator;

    actingAs($superUser)
      ->patchJson(route('day-cares.members.update', [$dayCare, $memberToUpdate]), [
        'role' => $role
      ])
      ->assertOk()
      ->assertJsonPath('data.role', $role->value);

    assertEquals(
      $role,
      $dayCare->members()->where('id', $memberToUpdate->id)->first()->pivot->role
    );
  });

  test('administrators can update members', function () {
    $dayCare = DayCare::factory()->create();

    $administrator = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Administrator
    ])->create();

    $memberToUpdate = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $role = DayCareMemberRole::Administrator;

    actingAs($administrator)
      ->patchJson(route('day-cares.members.update', [$dayCare, $memberToUpdate]), [
        'role' => $role
      ])
      ->assertOk()
      ->assertJsonPath('data.role', $role->value);

    assertEquals(
      $role,
      $dayCare->members()->where('id', $memberToUpdate->id)->first()->pivot->role
    );
  });

  test("contributors can't update members", function () {
    $dayCare = DayCare::factory()->create();

    $contributor = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $role = DayCareMemberRole::Contributor;

    $memberToUpdate = User::factory()->hasAttached($dayCare, [
      'role' => $role
    ])->create();

    actingAs($contributor)
      ->patchJson(route('day-cares.members.update', [$dayCare, $memberToUpdate]), [
        'role' => DayCareMemberRole::Administrator
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertEquals(
      $role,
      $dayCare->members()->where('id', $memberToUpdate->id)->first()->pivot->role
    );
  });

  test("users can't update members", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    $role = DayCareMemberRole::Contributor;

    $memberToUpdate = User::factory()->hasAttached($dayCare, [
      'role' => $role
    ])->create();

    actingAs($user)
      ->patchJson(route('day-cares.members.update', [$dayCare, $memberToUpdate]), [
        'role' => DayCareMemberRole::Administrator
      ])
      ->assertForbidden()
      ->assertJsonMissingPath('data');

    assertEquals(
      $role,
      $dayCare->members()->where('id', $memberToUpdate->id)->first()->pivot->role
    );
  });
});

describe('destroy', function () {
  test('super users can delete members', function () {
    $dayCare = DayCare::factory()->create();

    $member = DayCareMember::factory()->for($dayCare)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser)
      ->deleteJson(route('day-cares.members.destroy', [$dayCare, $member->user_id]))
      ->assertNoContent();

    assertTrue(
      $dayCare->members()
        ->where('id', $member->id)
        ->doesntExist()
    );
  });

  test('administrators can delete members', function () {
    $dayCare = DayCare::factory()->create();

    $member = DayCareMember::factory()->for($dayCare)->create();

    $administrator = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Administrator
    ])->create();

    actingAs($administrator)
      ->deleteJson(route('day-cares.members.destroy', [$dayCare, $member->user_id]))
      ->assertNoContent();

    assertTrue(
      $dayCare->members()
        ->where('id', $member->id)
        ->doesntExist()
    );
  });

  test("contributors can't delete members", function () {
    $dayCare = DayCare::factory()->create();

    $member = DayCareMember::factory()->for($dayCare)->create();

    $contributor = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($contributor)
      ->deleteJson(route('day-cares.members.destroy', [$dayCare, $member->user_id]))
      ->assertForbidden();
  });

  test("users can't delete members", function () {
    $dayCare = DayCare::factory()->create();

    $member = DayCareMember::factory()->for($dayCare)->create();

    $user = User::factory()->create();

    actingAs($user)
      ->deleteJson(route('day-cares.members.destroy', [$dayCare, $member->user_id]))
      ->assertForbidden();
  });
});
