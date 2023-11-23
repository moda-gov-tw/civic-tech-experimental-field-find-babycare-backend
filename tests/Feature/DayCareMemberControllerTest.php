<?php

use App\Enums\DayCareMemberRole;
use App\Models\DayCare;
use App\Models\DayCareMember;
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
    $dayCare = DayCare::factory()->create();

    DayCareMember::factory()->for($dayCare)->count(2)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    getJson(route('day-cares.members.index', $dayCare))
      ->assertOk()
      ->assertJson([
        'data' => $dayCare->members()->get()->only('id', 'name', 'email', 'role')->toArray()
      ]);
  });

  test("members can get members", function () {
    $dayCare = DayCare::factory()->create();

    DayCareMember::factory()->for($dayCare)->count(2)->create();

    $contributor = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($contributor);

    getJson(route('day-cares.members.index', $dayCare))
      ->assertOk()
      ->assertJson([
        'data' => $dayCare->members()->get()->only('id', 'name', 'email', 'role')->toArray()
      ]);
  });

  test("users can't get members", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    actingAs($user);

    getJson(route('day-cares.members.index', $dayCare))
      ->assertForbidden();
  });
});

describe('store', function () {
  test("super users can add members", function () {
    $dayCare = DayCare::factory()->create();

    $userToAdd = User::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    postJson(route('day-cares.members.store', $dayCare), [
      'email' => $userToAdd->email,
      'role' => DayCareMemberRole::Contributor
    ])->assertCreated();

    assertNotNull(
      $dayCare->members()
        ->wherePivot('user_id', $userToAdd->id)
        ->wherePivot('role', DayCareMemberRole::Contributor)
        ->first()
    );
  });

  test('administrators can add members', function () {
    $dayCare = DayCare::factory()->create();

    $userToAdd = User::factory()->create();

    $administrator = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Administrator
    ])->create();

    actingAs($administrator);

    postJson(route('day-cares.members.store', $dayCare), [
      'email' => $userToAdd->email,
      'role' => DayCareMemberRole::Contributor
    ])->assertCreated();

    assertNotNull(
      $dayCare->members()
        ->wherePivot('user_id', $userToAdd->id)
        ->wherePivot('role', DayCareMemberRole::Contributor)
        ->first()
    );
  });

  test("contributors can't add members", function () {
    $dayCare = DayCare::factory()->create();

    $contributor = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($contributor);

    postJson(route('day-cares.members.store', $dayCare))
      ->assertForbidden();
  });

  test("users can't add members", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    actingAs($user);

    postJson(route('day-cares.members.store', $dayCare))
      ->assertForbidden();
  });

  test("can't add member with a non existing email", function () {
    $dayCare = DayCare::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    postJson(route('day-cares.members.store', $dayCare), [
      'email' => 'nonexisting@email.com',
      'role' => DayCareMemberRole::Contributor
    ])->assertUnprocessable();
  });

  test("can't create member with an invalid role", function () {
    $dayCare = DayCare::factory()->create();

    $userToInvite = User::factory()->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    postJson(route('day-cares.members.store', $dayCare), [
      'email' => $userToInvite->email,
      'role' => 'invalid-role'
    ])->assertUnprocessable();
  });
});

describe('show', function () {
  test('super users can get member', function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    getJson(route('day-cares.members.show', [$dayCare, $member]))
      ->assertOk()
      ->assertJson(['data' => $member->only('id')]);
  });

  test('members can get member', function () {
    $dayCare = DayCare::factory()->create();

    $memberToShow = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($member);

    getJson(route('day-cares.members.show', [$dayCare, $memberToShow]))
      ->assertOk()
      ->assertJson(['data' => $memberToShow->only('id')]);
  });

  test("users can't get members", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $user = User::factory()->create();

    actingAs($user);

    getJson(route('day-cares.members.show', [$dayCare, $member]))
      ->assertForbidden();
  });
});

describe('update', function () {
  test("super users can update members", function () {
    $dayCare = DayCare::factory()->create();

    $superUser = User::factory()->superUser()->create();

    $memberToUpdate = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($superUser);

    patchJson(route('day-cares.members.update', [$dayCare, $memberToUpdate]), [
      'role' => DayCareMemberRole::Administrator
    ])->assertOk();

    assertEquals(
      DayCareMemberRole::Administrator,
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

    actingAs($administrator);

    patchJson(route('day-cares.members.update', [$dayCare, $memberToUpdate]), [
      'role' => DayCareMemberRole::Administrator
    ])->assertOk();

    assertEquals(
      DayCareMemberRole::Administrator,
      $dayCare->members()->where('id', $memberToUpdate->id)->first()->pivot->role
    );
  });

  test("contributors can't update members", function () {
    $dayCare = DayCare::factory()->create();

    $contributor = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $memberToUpdate = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($contributor);

    patchJson(route('day-cares.members.update', [$dayCare, $memberToUpdate]), [
      'role' => DayCareMemberRole::Administrator
    ])->assertForbidden();
  });

  test("users can't update members", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    $memberToUpdate = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($user);

    patchJson(route('day-cares.members.update', [$dayCare, $memberToUpdate]), [
      'role' => DayCareMemberRole::Administrator
    ])->assertForbidden();
  });
});

describe('destroy', function () {
  test('super users can delete members', function () {
    $dayCare = DayCare::factory()->create();

    $member = DayCareMember::factory()->for($dayCare)->create();

    $superUser = User::factory()->superUser()->create();

    actingAs($superUser);

    deleteJson(route('day-cares.members.destroy', [$dayCare, $member->user_id]))
      ->assertOk();

    assertNull($dayCare->members()->where('id', $member->id)->first());
  });

  test('administrators can delete members', function () {
    $dayCare = DayCare::factory()->create();

    $member = DayCareMember::factory()->for($dayCare)->create();

    $administrator = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Administrator
    ])->create();

    actingAs($administrator);

    deleteJson(route('day-cares.members.destroy', [$dayCare, $member->user_id]))
      ->assertOk();

    assertNull($dayCare->members()->where('id', $member->id)->first());
  });

  test("contributors can't delete members", function () {
    $dayCare = DayCare::factory()->create();

    $member = DayCareMember::factory()->for($dayCare)->create();

    $contributor = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($contributor);

    deleteJson(route('day-cares.members.destroy', [$dayCare, $member->user_id]))
      ->assertForbidden();
  });

  test("users can't delete members", function () {
    $dayCare = DayCare::factory()->create();

    $member = DayCareMember::factory()->for($dayCare)->create();

    $user = User::factory()->create();

    actingAs($user);

    deleteJson(route('day-cares.members.destroy', [$dayCare, $member->user_id]))
      ->assertForbidden();
  });
});
