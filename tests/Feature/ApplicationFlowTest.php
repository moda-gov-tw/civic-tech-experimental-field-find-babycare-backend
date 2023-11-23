<?php

use App\Enums\DayCareMemberRole;
use App\Models\ApplicationDraft;
use App\Models\DayCare;
use App\Models\Infant;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test('application flow', function () {
  $dayCare = DayCare::factory()->create();

  $member = User::factory()->hasAttached($dayCare, [
    'role' => DayCareMemberRole::Contributor
  ])->create();

  $user = User::factory()->create();

  $infant = Infant::factory()->create();

  $draft = ApplicationDraft::factory()
    ->for($user)
    ->hasAttached($dayCare)
    ->hasAttached($infant)
    ->create();

  actingAs($user);

  postJson(route('application-drafts.submit', $draft))
    ->assertOk();

  $application = $user->applications()->first();

  actingAs($member);

  postJson(route('day-cares.applications.approve', [$dayCare, $application]))
    ->assertOk();

  postJson(route('day-cares.applications.accept', [$dayCare, $application]))
    ->assertOk();

  actingAs($user);

  postJson(route('applications.register', $application))
    ->assertOk();
});
