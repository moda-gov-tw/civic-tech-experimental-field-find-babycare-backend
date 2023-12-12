<?php

use App\Enums\ApplicationStatus;
use App\Enums\DayCareMemberRole;
use App\Models\Application;
use App\Models\DayCare;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertEquals;

describe('withdraw', function () {
    test("application's owner can withdraw waitlist approved application", function () {
        $user = User::factory()->create();

        $application = Application::factory()->for($user)->create([
            'status' => ApplicationStatus::WaitlistApproved
        ]);

        actingAs($user)
            ->postJson(route('applications.withdraw', $application))
            ->assertOk();

        assertEquals(ApplicationStatus::Withdrew, $application->refresh()->status);
    });

    test("application's owner can withdraw raffle approved application", function () {
        $user = User::factory()->create();

        $application = Application::factory()->for($user)->create([
            'status' => ApplicationStatus::RaffleApproved
        ]);

        actingAs($user)
            ->postJson(route('applications.withdraw', $application))
            ->assertOk();

        assertEquals(ApplicationStatus::Withdrew, $application->refresh()->status);
    });

    test("only waitlist or raffle approved applications can be withdrawn", function () {
        $user = User::factory()->create();

        $statuses = array_diff(ApplicationStatus::values(), [
            ApplicationStatus::RaffleApproved->value,
            ApplicationStatus::WaitlistApproved->value
        ]);

        foreach ($statuses as $status) {
            $application = Application::factory()->for($user)->create([
                'status' => $status
            ]);

            actingAs($user)
                ->postJson(route('applications.withdraw', $application))
                ->assertForbidden();

            assertEquals($status, $application->refresh()->status->value);
        }
    });

    test("day care's members can't withdraw applications", function () {
        $dayCare = DayCare::factory()->create();

        $member = User::factory()->hasAttached($dayCare, [
            'role' => DayCareMemberRole::Contributor
        ])->create();

        $status = ApplicationStatus::WaitlistApproved;

        $application = Application::factory()->for($dayCare)->create([
            'status' => $status
        ]);

        actingAs($member)
            ->postJson(route('applications.withdraw', $application))
            ->assertForbidden();

        assertEquals($status, $application->refresh()->status);
    });

    test("users can't withdraw applications", function () {
        $dayCare = DayCare::factory()->create();

        $status = ApplicationStatus::WaitlistApproved;

        $application = Application::factory()->for($dayCare)->create([
            'status' => $status
        ]);

        $user = User::factory()->create();

        actingAs($user)
            ->postJson(route('applications.withdraw', $application))
            ->assertForbidden();

        assertEquals($status, $application->refresh()->status);
    });
});

describe('forfeit', function () {
    test("application's owner can forfeit accepted application", function () {
        $user = User::factory()->create();

        $application = Application::factory()->for($user)->create([
            'status' => ApplicationStatus::Accepted
        ]);

        actingAs($user)
            ->postJson(route('applications.forfeit', $application))
            ->assertOk();

        assertEquals(ApplicationStatus::Forfeited, $application->refresh()->status);
    });

    test("application's owner can forfeit registration returned application", function () {
        $user = User::factory()->create();

        $application = Application::factory()->for($user)->create([
            'status' => ApplicationStatus::RegistrationReturned
        ]);

        actingAs($user)
            ->postJson(route('applications.forfeit', $application))
            ->assertOk();

        assertEquals(ApplicationStatus::Forfeited, $application->refresh()->status);
    });

    test("only accepted or registration returned applications can be forfeited", function () {
        $user = User::factory()->create();

        $statuses = array_diff(ApplicationStatus::values(), [
            ApplicationStatus::Accepted->value,
            ApplicationStatus::RegistrationReturned->value
        ]);

        foreach ($statuses as $status) {
            $application = Application::factory()->for($user)->create([
                'status' => $status
            ]);

            actingAs($user)
                ->postJson(route('applications.forfeit', $application))
                ->assertForbidden();

            assertEquals($status, $application->refresh()->status->value);
        }
    });

    test("day care's members can't forfeit applications", function () {
        $dayCare = DayCare::factory()->create();

        $member = User::factory()->hasAttached($dayCare, [
            'role' => DayCareMemberRole::Contributor
        ])->create();

        $application = Application::factory()->for($dayCare)->create([
            'status' => ApplicationStatus::Accepted
        ]);

        actingAs($member)
            ->postJson(route('applications.forfeit', $application))
            ->assertForbidden();

        assertEquals(ApplicationStatus::Accepted, $application->refresh()->status);
    });

    test("users can't forfeit applications", function () {
        $application = Application::factory()->create([
            'status' => ApplicationStatus::Accepted
        ]);

        $user = User::factory()->create();

        actingAs($user)
            ->postJson(route('applications.forfeit', $application))
            ->assertForbidden();

        assertEquals(ApplicationStatus::Accepted, $application->refresh()->status);
    });
});

describe('resubmit', function () {
    test("application's owner can resubmit application returned applications", function () {
        $user = User::factory()->create();

        $application = Application::factory()->for($user)->create([
            'status' => ApplicationStatus::ApplicationReturned
        ]);

        actingAs($user)
            ->postJson(route('applications.resubmit', $application))
            ->assertOk();

        assertEquals(ApplicationStatus::Submitted, $application->refresh()->status);
    });

    test("application's owner can resubmit registration returned applications", function () {
        $user = User::factory()->create();

        $application = Application::factory()->for($user)->create([
            'status' => ApplicationStatus::RegistrationReturned
        ]);

        actingAs($user)
            ->postJson(route('applications.resubmit', $application))
            ->assertOk();

        assertEquals(ApplicationStatus::Registered, $application->refresh()->status);
    });

    test("only application returned or registration returned applications can be resubmitted", function () {
        $user = User::factory()->create();

        $statuses = array_diff(ApplicationStatus::values(), [
            ApplicationStatus::ApplicationReturned->value,
            ApplicationStatus::RegistrationReturned->value
        ]);

        foreach ($statuses as $status) {
            $application = Application::factory()->for($user)->create([
                'status' => $status
            ]);

            actingAs($user)
                ->postJson(route('applications.resubmit', $application))
                ->assertForbidden();

            assertEquals($status, $application->refresh()->status->value);
        }
    });

    test("day care's members can't resubmit applications", function () {
        $dayCare = DayCare::factory()->create();

        $member = User::factory()->hasAttached($dayCare, [
            'role' => DayCareMemberRole::Contributor
        ])->create();

        $application = Application::factory()->for($dayCare)->create([
            'status' => ApplicationStatus::ApplicationReturned
        ]);

        actingAs($member)
            ->postJson(route('applications.resubmit', $application))
            ->assertForbidden();

        assertEquals(ApplicationStatus::ApplicationReturned, $application->refresh()->status);
    });

    test("users can't resubmit applications", function () {
        $application = Application::factory()->create([
            'status' => ApplicationStatus::ApplicationReturned
        ]);

        $user = User::factory()->create();

        actingAs($user)
            ->postJson(route('applications.resubmit', $application))
            ->assertForbidden();

        assertEquals(ApplicationStatus::ApplicationReturned, $application->refresh()->status);
    });
});

describe('register', function () {
});
