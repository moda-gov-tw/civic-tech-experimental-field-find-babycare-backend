<?php

use App\Models\Address;
use App\Models\AdministrativeGroup;
use App\Models\AdministrativeGroupMember;
use App\Models\Application;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDayCareDocument;
use App\Models\ApplicationDraftDocument;
use App\Models\ApplicationDraftInfantDocument;
use App\Models\DayCare;
use App\Models\DayCareMember;
use App\Models\Infant;
use App\Models\InfantStatus;
use App\Models\User;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

describe('Users', function () {
    test('user has administrative groups', function () {
        $user = User::factory()->create();

        $group = AdministrativeGroup::factory()->create();

        AdministrativeGroupMember::factory()->for($group)->for($user)->create();

        assertEquals(1, $user->administrativeGroups()->count());
        assertEquals($user->administrativeGroups()->first()->id, $group->id);
    });

    test('user has day cares', function () {
        $user = User::factory()->create();

        $dayCare = DayCare::factory()->create();

        DayCareMember::factory()->for($user)->for($dayCare)->create();

        assertEquals(1, $user->dayCares()->count());
        assertEquals($user->dayCares()->first()->id, $dayCare->id);
    });

    test('user has application drafts', function () {
        $user = User::factory()->create();

        $drafts = ApplicationDraft::factory()->for($user)->count(2)->create();

        assertEquals(
            $drafts->pluck('id')->toArray(),
            $user->applicationDrafts()->select('application_drafts.id')->get()->pluck('id')->toArray()
        );
    });

    test('user has applications', function () {
        $user = User::factory()->create();

        $applications = Application::factory()->for($user)->count(4)->create();

        assertEquals(
            $applications->pluck('id')->toArray(),
            $user->applications()->select('id')->get()->pluck('id')->toArray()
        );
    });
});

describe('Day cares', function () {
    test('day care has members', function () {
        $dayCare = DayCare::factory()->create();

        $members = DayCareMember::factory()->for($dayCare)->count(4)->create();

        assertEquals($dayCare->members()->count(), sizeof($members));
    });

    test('day care has address', function () {
        $address = Address::factory()->create();

        $dayCare = DayCare::factory()->for($address)->create();

        assertEquals($address->id, $dayCare->address->id);
    });

    test('day care has applications', function () {
        $dayCare = DayCare::factory()->create();

        $applications = Application::factory()->for($dayCare)->count(4)->create();

        assertEquals(
            $applications->pluck('id')->toArray(),
            $dayCare->applications()->select('id')->get()->pluck('id')->toArray()
        );
    });
});

describe('Application drafts', function () {
    test('user has application drafts', function () {
        $user = User::factory()
            ->has(ApplicationDraft::factory()->count(2)) // hasApplicationDrafts(2)
            ->create();

        $draft = ApplicationDraft::factory()->for($user)->count(2);

        assertEquals(sizeof($user->applicationDrafts), 2);
    });

    test('application draft has user', function () {
        $draft = ApplicationDraft::factory()->create();

        assertNotNull($draft->user);
    });

    test('application draft has day cares', function () {
        $dayCares = DayCare::factory()->count(2)->create();

        $draft = ApplicationDraft::factory()->hasAttached($dayCares)->create();

        assertEquals(
            $dayCares->pluck('id')->toArray(),
            $draft->dayCares()->select('day_cares.id')->get()->pluck('id')->toArray()
        );
    });

    test('application drafts have documents', function () {
        $draft = ApplicationDraft::factory()->create();

        $documents = ApplicationDraftDocument::factory()->for($draft)->count(4)->create();

        assertEquals(
            $documents->pluck('id')->toArray(),
            $draft->documents()->select('id')->get()->pluck('id')->toArray()
        );
    });

    test('application drafts have day care documents', function () {
        $draft = ApplicationDraft::factory()->create();

        $dayCareDocuments = ApplicationDraftDayCareDocument::factory()->for($draft)->count(4)->create();

        assertEquals(
            $dayCareDocuments->pluck('id')->toArray(),
            $draft->dayCareDocuments()->select('id')->get()->pluck('id')->toArray()
        );
    });

    test('application drafts have infant documents', function () {
        $draft = ApplicationDraft::factory()->create();

        $ifantDocuments = ApplicationDraftInfantDocument::factory()->for($draft)->count(4)->create();

        assertEquals(
            $ifantDocuments->pluck('id')->toArray(),
            $draft->infantDocuments()->select('id')->get()->pluck('id')->toArray()
        );
    });

    test('application draft has infants', function () {
        $infants = Infant::factory()->count(2)->create();

        $draft = ApplicationDraft::factory()->hasAttached($infants)->create();

        assertEquals(
            $infants->pluck('id')->toArray(),
            $draft->infants->pluck('id')->toArray()
        );
    });
});

describe('Infants', function () {
    test('infant can have address', function () {
        $infantWithoutAddress = Infant::factory()->create();

        $address = Address::factory()->create();

        $infantWithAddress = Infant::factory()->for($address)->create();

        assertNull($infantWithoutAddress->address);
        assertNotNull($infantWithAddress->address);
        assertEquals($address->id, $infantWithAddress->address->id);
    });

    test('infant can have application drafts', function () {
        $drafts = ApplicationDraft::factory()->count(2)->create();

        $infant = Infant::factory()->hasAttached($drafts)->create();

        assertEquals(
            $drafts->pluck('id')->toArray(),
            $infant->applicationDrafts->pluck('id')->toArray()
        );
    });

    test('infants can have statuses', function () {
        $infant = Infant::factory()->create();

        $statuses = InfantStatus::factory()->for($infant)->count(2)->create();

        assertEquals(
            $statuses->pluck('id')->toArray(),
            $infant->statuses->pluck('id')->toArray()
        );
    })->skip();
});

describe('Infant status', function () {
    test('infant statuses have infant', function () {
        $status = InfantStatus::factory()->create();

        assertNotNull($status->infant);
    });
});
