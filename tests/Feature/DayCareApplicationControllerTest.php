<?php

use App\Enums\ApplicationStatus;
use App\Enums\DayCareMemberRole;
use App\Models\Application;
use App\Models\DayCare;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertEquals;

describe('index', function () {
  test("day care's members can see the day care's applications", function () {
    $dayCare = DayCare::factory()->create();

    $applications = Application::factory()->for($dayCare)->count(2)->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($member)
      ->getJson(route('day-cares.applications.index', $dayCare))
      ->assertOk()
      ->assertJson([
        'data' => $applications->only('id')->toArray()
      ]);
  });

  test("users can't see the day care's applications", function () {
    $dayCare = DayCare::factory()->hasApplications(2)->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('day-cares.applications.index', $dayCare))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('show', function () {
  test("day care's members can see application", function () {
    $dayCare = DayCare::factory()->create();

    $application = Application::factory()->for($dayCare)->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    actingAs($member)
      ->getJson(route('day-cares.applications.show', [$dayCare, $application]))
      ->assertOk()
      ->assertJsonPath('data.id', $application->id);
  });

  test("users can't see application", function () {
    $dayCare = DayCare::factory()->create();

    $application = Application::factory()->for($dayCare)->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('day-cares.applications.show', [$dayCare, $application]))
      ->assertForbidden()
      ->assertJsonMissingPath('data');
  });
});

describe('return', function () {
  test('day care members can return submitted applications', function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $application = Application::factory()->for($dayCare)->create();

    actingAs($member)
      ->postJson(route('day-cares.applications.return', [$dayCare, $application]))
      ->assertOk();

    assertEquals(ApplicationStatus::ApplicationReturned, $application->refresh()->status);
  });

  test('day care members can return registered applications', function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $application = Application::factory()->for($dayCare)->create([
      'status' => ApplicationStatus::Registered
    ]);

    actingAs($member)
      ->postJson(route('day-cares.applications.return', [$dayCare, $application]))
      ->assertOk();

    assertEquals(ApplicationStatus::RegistrationReturned, $application->refresh()->status);
  });

  test("only submitted and registered applications can be returned", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $statuses = array_diff(ApplicationStatus::values(), [
      ApplicationStatus::Submitted->value,
      ApplicationStatus::Registered->value
    ]);

    foreach ($statuses as $status) {
      $application = Application::factory()->for($dayCare)->create([
        'status' => $status
      ]);

      actingAs($member)
        ->postJson(route('day-cares.applications.return', [$dayCare, $application]))
        ->assertForbidden();

      assertEquals($status, $application->refresh()->status->value);
    }
  });

  test("application's owner can't return application", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    $application = Application::factory()->for($user)->for($dayCare)->create([
      'status' => ApplicationStatus::Registered
    ]);

    actingAs($user)
      ->postJson(route('day-cares.applications.return', [$dayCare, $application]))
      ->assertForbidden();

    assertEquals(ApplicationStatus::Registered, $application->refresh()->status);
  });

  test("users can't return application", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    $application = Application::factory()->for($dayCare)->create([
      'status' => ApplicationStatus::Registered
    ]);

    actingAs($user)
      ->postJson(route('day-cares.applications.return', [$dayCare, $application]))
      ->assertForbidden();

    assertEquals(ApplicationStatus::Registered, $application->refresh()->status);
  });
});

describe('approve', function () {
  test('day care members can approve submitted applications', function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $application = Application::factory()->for($dayCare)->create();

    actingAs($member)
      ->postJson(route('day-cares.applications.approve', [$dayCare, $application]))
      ->assertOk();

    assertEquals(ApplicationStatus::WaitlistApproved, $application->refresh()->status);
  });

  test("only submitted applications can be approved", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $statuses = array_diff(ApplicationStatus::values(), [ApplicationStatus::Submitted->value]);

    foreach ($statuses as $status) {
      $application = Application::factory()->for($dayCare)->create([
        'status' => $status
      ]);

      actingAs($member)
        ->postJson(route('day-cares.applications.approve', [$dayCare, $application]))
        ->assertForbidden();

      assertEquals($status, $application->refresh()->status->value);
    }
  });

  test("status should be raffle or waitlist approved depending on the day care state", function () {
    $dayCare = DayCare::factory()->create(['is_in_raffle' => true]);

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $raffleApplication = Application::factory()->for($dayCare)->create();

    actingAs($member)
      ->postJson(route('day-cares.applications.approve', [$dayCare, $raffleApplication]))
      ->assertOk();

    assertEquals(ApplicationStatus::RaffleApproved, $raffleApplication->refresh()->status);

    $dayCare->update(['is_in_raffle' => false]);

    $waitlistApplication = Application::factory()->for($dayCare)->create();

    actingAs($member)
      ->postJson(route('day-cares.applications.approve', [$dayCare, $waitlistApplication]))
      ->assertOk();

    assertEquals(ApplicationStatus::WaitlistApproved, $waitlistApplication->refresh()->status);
  });

  test("application's owner can't approve application", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    $application = Application::factory()->for($user)->for($dayCare)->create();

    actingAs($user)
      ->postJson(route('day-cares.applications.approve', [$dayCare, $application]))
      ->assertForbidden();

    assertEquals(ApplicationStatus::Submitted, $application->refresh()->status);
  });

  test("users can't approve application", function () {
    $dayCare = DayCare::factory()->create();

    $application = Application::factory()->for($dayCare)->create();

    $user = User::factory()->create();

    actingAs($user)
      ->postJson(route('day-cares.applications.approve', [$dayCare, $application]))
      ->assertForbidden();

    assertEquals(ApplicationStatus::Submitted, $application->refresh()->status);
  });
});

describe('accept', function () {
  test("day care members can accept waitlist approved application", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $application = Application::factory()->for($dayCare)->create([
      'status' => ApplicationStatus::WaitlistApproved
    ]);

    actingAs($member)
      ->postJson(route('day-cares.applications.accept', [$dayCare, $application]))
      ->assertOk();

    assertEquals(ApplicationStatus::Accepted, $application->refresh()->status);
  });

  test("day care members can accept raffle approved application", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $application = Application::factory()->for($dayCare)->create([
      'status' => ApplicationStatus::RaffleApproved
    ]);

    actingAs($member)
      ->postJson(route('day-cares.applications.accept', [$dayCare, $application]))
      ->assertOk();

    assertEquals(ApplicationStatus::Accepted, $application->refresh()->status);
  });

  test("only waitlist and raffle approved applications can be accepted", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $statuses = array_diff(ApplicationStatus::values(), [
      ApplicationStatus::RaffleApproved->value,
      ApplicationStatus::WaitlistApproved->value
    ]);

    foreach ($statuses as $status) {
      $application = Application::factory()->for($dayCare)->create([
        'status' => $status
      ]);

      actingAs($member)
        ->postJson(route('day-cares.applications.accept', [$dayCare, $application]))
        ->assertForbidden();

      assertEquals($status, $application->refresh()->status->value);
    }
  });

  test("application's owner can't accept application", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    $application = Application::factory()->for($user)->for($dayCare)->create([
      'status' => ApplicationStatus::WaitlistApproved
    ]);

    actingAs($user)
      ->postJson(route('day-cares.applications.accept', [$dayCare, $application]))
      ->assertForbidden();

    assertEquals(ApplicationStatus::WaitlistApproved, $application->refresh()->status);
  });

  test("users can't accept application", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    $application = Application::factory()->for($dayCare)->create([
      'status' => ApplicationStatus::WaitlistApproved
    ]);

    actingAs($user)
      ->postJson(route('day-cares.applications.accept', [$dayCare, $application]))
      ->assertForbidden();

    assertEquals(ApplicationStatus::WaitlistApproved, $application->refresh()->status);
  });
});

describe('reject', function () {
  test("day care members can reject registered applications", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $application = Application::factory()->for($dayCare)->create([
      'status' => ApplicationStatus::Registered
    ]);

    actingAs($member)
      ->postJson(route('day-cares.applications.reject', [$dayCare, $application]))
      ->assertOk();

    assertEquals(ApplicationStatus::Rejected, $application->refresh()->status);
  });

  test("day care members can reject waitlist approved applications", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $application = Application::factory()->for($dayCare)->create([
      'status' => ApplicationStatus::WaitlistApproved
    ]);

    actingAs($member)
      ->postJson(route('day-cares.applications.reject', [$dayCare, $application]))
      ->assertOk();

    assertEquals(ApplicationStatus::Rejected, $application->refresh()->status);
  });

  test("day care members can reject raffle approved applications", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $application = Application::factory()->for($dayCare)->create([
      'status' => ApplicationStatus::RaffleApproved
    ]);

    actingAs($member)
      ->postJson(route('day-cares.applications.reject', [$dayCare, $application]))
      ->assertOk();

    assertEquals(ApplicationStatus::Rejected, $application->refresh()->status);
  });

  test("only registered, waitlist and raffle approved applications can be rejected", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $statuses = array_diff(ApplicationStatus::values(), [
      ApplicationStatus::Registered->value,
      ApplicationStatus::RaffleApproved->value,
      ApplicationStatus::WaitlistApproved->value
    ]);

    foreach ($statuses as $status) {
      $application = Application::factory()->for($dayCare)->create([
        'status' => $status
      ]);

      actingAs($member)
        ->postJson(route('day-cares.applications.reject', [$dayCare, $application]))
        ->assertForbidden();

      assertEquals($status, $application->refresh()->status->value);
    }
  });

  test("application's owner can't reject application", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    $application = Application::factory()->for($user)->for($dayCare)->create([
      'status' => ApplicationStatus::Registered
    ]);

    actingAs($user)
      ->postJson(route('day-cares.applications.reject', [$dayCare, $application]))
      ->assertForbidden();

    assertEquals(ApplicationStatus::Registered, $application->refresh()->status);
  });

  test("users can't accept applications", function () {
    $dayCare = DayCare::factory()->create();

    $user = User::factory()->create();

    $application = Application::factory()->for($dayCare)->create([
      'status' => ApplicationStatus::Registered
    ]);

    actingAs($user)
      ->postJson(route('day-cares.applications.reject', [$dayCare, $application]))
      ->assertForbidden();

    assertEquals(ApplicationStatus::Registered, $application->refresh()->status);
  });
});

describe('enroll', function () {
});
