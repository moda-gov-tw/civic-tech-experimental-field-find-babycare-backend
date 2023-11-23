<?php

use App\Enums\DayCareMemberRole;
use App\Models\Application;
use App\Models\ApplicationDayCareDocument;
use App\Models\DayCare;
use App\Models\User;

use function Pest\Laravel\actingAs;

describe('show', function () {
  test("application's day care's members can see application's documents", function () {
    $dayCare = DayCare::factory()->create();

    $member = User::factory()->hasAttached($dayCare, [
      'role' => DayCareMemberRole::Contributor
    ])->create();

    $application = Application::factory()
      ->for($dayCare)
      ->create();

    $document = ApplicationDayCareDocument::factory()
      ->for($application)
      ->create();

    actingAs($member)
      ->getJson(route('applications.day-care-documents.show', [$application, $document]))
      ->assertOk();
  });

  test("application's owner can see application's documents", function () {
    $user = User::factory()->create();

    $application = Application::factory()
      ->for($user)
      ->create();

    $document = ApplicationDayCareDocument::factory()
      ->for($application)
      ->create();

    actingAs($user)
      ->getJson(route('applications.day-care-documents.show', [$application, $document]))
      ->assertOk();
  });

  test("users can't see application's documents", function () {
    $application = Application::factory()->create();

    $document = ApplicationDayCareDocument::factory()
      ->for($application)
      ->create();

    $user = User::factory()->create();

    actingAs($user)
      ->getJson(route('applications.day-care-documents.show', [$application, $document]))
      ->assertForbidden();
  });
});
