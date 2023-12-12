<?php

use App\Enums\DayCareMemberRole;
use App\Models\Application;
use App\Models\ApplicationDocument;
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

        $document = ApplicationDocument::factory()
            ->for($application)
            ->create();

        actingAs($member)
            ->getJson(route('applications.documents.show', [$application, $document]))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');
    });

    test("application's owner can see application's documents", function () {
        $user = User::factory()->create();

        $application = Application::factory()
            ->for($user)
            ->create();

        $document = ApplicationDocument::factory()
            ->for($application)
            ->create();

        actingAs($user)
            ->getJson(route('applications.documents.show', [$application, $document]))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');
    });

    test("users can't see application's documents", function () {
        $application = Application::factory()->create();

        $document = ApplicationDocument::factory()
            ->for($application)
            ->create();

        $user = User::factory()->create();

        actingAs($user)
            ->getJson(route('applications.documents.show', [$application, $document]))
            ->assertForbidden();
    });
});
