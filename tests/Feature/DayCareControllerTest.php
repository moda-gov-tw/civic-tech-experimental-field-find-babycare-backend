<?php

use App\Enums\DayCareCategory;
use App\Enums\DayCareMemberRole;
use App\Enums\DayCareType;
use App\Models\DayCare;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

describe('index', function () {
    test('members can see their day cares', function () {
        DayCare::factory()->count(2)->create();

        $dayCares = DayCare::factory()->count(2)->create();

        $user = User::factory()
            ->hasAttached($dayCares, [
                'role' => DayCareMemberRole::Contributor
            ])
            ->create();

        actingAs($user);

        getJson(route('day-cares.index'))
            ->assertOk()
            ->assertJson([
                'data' => $dayCares->only('id')->toArray()
            ]);
    });
});

describe('store', function () {
    test('super users can create day care', function () {
        $superUser = User::factory()->superUser()->create();

        actingAs($superUser);

        postJson(route('day-cares.store'), [
            'name' => 'Day care name',
            'type' => DayCareType::Public,
            'is_in_construction' => false,
            'is_in_raffle' => false,
            'is_accepting_applications' => true,
            'category' => DayCareCategory::Center,
            'operating_hours' => 'Day care operating hours',
            'capacity' => 30,
            'monthly_fees' => 3005.25,
            'establishment_id' => 'Day care establishment id',
            'phone' => 'Day care phone',
            'fax' => 'Day care fax',
            'email' => 'contact@daycare.com',
            'lat' => 0,
            'lon' => 0,
            'facebook_page_url' => 'http://daycare.com',
            'address' => [
                'city' => 'City',
                'district' => 'District',
                'street' => 'Street'
            ]
        ])->assertCreated();
    });

    test("users can't create day care", function () {
        $user = User::factory()->create();

        actingAs($user);

        postJson(route('day-cares.store'))->assertForbidden();
    });
});

describe('show', function () {
    test('members can see their day care', function () {
        $dayCare = DayCare::factory()->create();

        $contributor = User::factory()
            ->hasAttached($dayCare, ['role' => DayCareMemberRole::Contributor])
            ->create();

        actingAs($contributor);

        getJson(route('day-cares.show', $dayCare))
            ->assertOk()
            ->assertJson(['data' => $dayCare->only('id')]);
    });

    test("users can't see day care", function () {
        $dayCare = DayCare::factory()->create();

        $user = User::factory()->create();

        actingAs($user);

        getJson(route('day-cares.show', $dayCare))
            ->assertForbidden();
    });
});

describe('update', function () {
    test('super uses can update day care', function () {
        $dayCare = DayCare::factory()->create([
            'name' => 'Original day care name'
        ]);

        $superUser = User::factory()->superUser()->create();

        actingAs($superUser);

        patchJson(route('day-cares.update', $dayCare), [
            'name' => 'New day care name'
        ])->assertOk();
    });

    test('administrators can update day care', function () {
        $dayCare = DayCare::factory()->create([
            'name' => 'Original day care name'
        ]);

        $administrator = User::factory()
            ->hasAttached($dayCare, ['role' => DayCareMemberRole::Administrator])
            ->create();

        actingAs($administrator);

        patchJson(route('day-cares.update', $dayCare), [
            'name' => 'New day care name'
        ])->assertOk();
    });

    test("contributors can't update day care", function () {
        $dayCare = DayCare::factory()->create();

        $contributor = User::factory()
            ->hasAttached($dayCare, ['role' => DayCareMemberRole::Contributor])
            ->create();

        actingAs($contributor);

        patchJson(route('day-cares.update', $dayCare))
            ->assertForbidden();
    });

    test("users can't update day care", function () {
        $dayCare = DayCare::factory()->create();

        $user = User::factory()->create();

        actingAs($user);

        patchJson(route('day-cares.update', $dayCare))
            ->assertForbidden();
    });
});

describe('destroy', function () {
    test('super users can delete day care', function () {
        $dayCare = DayCare::factory()->create();

        $superUser = User::factory()->superUser()->create();

        actingAs($superUser);

        deleteJson(route('day-cares.destroy', $dayCare))
            ->assertOk();

        assertModelMissing($dayCare);
    });

    test("members can't delete day care", function () {
        $dayCare = DayCare::factory()->create();

        $administrator = User::factory()
            ->hasAttached($dayCare, [
                'role' => DayCareMemberRole::Administrator
            ])
            ->create();

        actingAs($administrator);

        deleteJson(route('day-cares.destroy', $dayCare))
            ->assertForbidden();
    });

    test("users can't delete day care", function () {
        $dayCare = DayCare::factory()->create();

        $user = User::factory()->create();

        actingAs($user);

        deleteJson(route('day-cares.destroy', $dayCare))
            ->assertForbidden();
    });
});
