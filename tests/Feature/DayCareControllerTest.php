<?php

use App\Enums\DayCareCategory;
use App\Enums\DayCareMemberRole;
use App\Enums\DayCareType;
use App\Models\DayCare;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function PHPUnit\Framework\assertEquals;

describe('index', function () {
    test('super users can see all day cares', function () {
        $dayCares = DayCare::factory()->count(2)->create();

        $superUser = User::factory()->superUser()->create();

        actingAs($superUser)
            ->getJson(route('day-cares.index'))
            ->assertOk()
            ->assertJson([
                'data' => $dayCares->only('id')->toArray()
            ]);
    });

    test('members can see their day cares', function () {
        DayCare::factory()->count(2)->create();

        $dayCares = DayCare::factory()->count(2)->create();

        $user = User::factory()->hasAttached($dayCares, [
            'role' => DayCareMemberRole::Contributor
        ])->create();

        actingAs($user)
            ->getJson(route('day-cares.index'))
            ->assertOk()
            ->assertJson([
                'data' => $dayCares->only('id')->toArray()
            ]);
    });
});

describe('store', function () {
    test('super users can create day care', function () {
        $superUser = User::factory()->superUser()->create();

        $name = 'Day care name';

        actingAs($superUser)
            ->postJson(route('day-cares.store'), [
                'name' => $name,
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
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', $name);
    });

    test("users can't create day care", function () {
        $user = User::factory()->create();

        actingAs($user)
            ->postJson(route('day-cares.store'))
            ->assertForbidden()
            ->assertJsonMissingPath('data');
    });
});

describe('show', function () {
    test('members can see their day care', function () {
        $dayCare = DayCare::factory()->create();

        $contributor = User::factory()->hasAttached($dayCare, [
            'role' => DayCareMemberRole::Contributor
        ])->create();

        actingAs($contributor)
            ->getJson(route('day-cares.show', $dayCare))
            ->assertOk()
            ->assertJsonPath('data.id', $dayCare->id);
    });

    test("users can't see day care", function () {
        $dayCare = DayCare::factory()->create();

        $user = User::factory()->create();

        actingAs($user)
            ->getJson(route('day-cares.show', $dayCare))
            ->assertForbidden()
            ->assertJsonMissingPath('data');
    });
});

describe('update', function () {
    test('super uses can update day care', function () {
        $dayCare = DayCare::factory()->create([
            'name' => 'Original day care name'
        ]);

        $superUser = User::factory()->superUser()->create();

        $name = 'New day care name';

        actingAs($superUser)
            ->patchJson(route('day-cares.update', $dayCare), [
                'name' => $name
            ])
            ->assertOk()
            ->assertJsonPath('data.name', $name);

        assertEquals($name, $dayCare->refresh()->name);
    });

    test('administrators can update day care', function () {
        $dayCare = DayCare::factory()->create([
            'name' => 'Original day care name'
        ]);

        $administrator = User::factory()->hasAttached($dayCare, [
            'role' => DayCareMemberRole::Administrator
        ])->create();

        $name = 'New day care name';

        actingAs($administrator)
            ->patchJson(route('day-cares.update', $dayCare), [
                'name' => $name
            ])
            ->assertOk()
            ->assertJsonPath('data.name', $name);

        assertEquals($name, $dayCare->refresh()->name);
    });

    test("contributors can't update day care", function () {
        $name = 'Original day care name';

        $dayCare = DayCare::factory()->create([
            'name' => $name
        ]);

        $contributor = User::factory()->hasAttached($dayCare, [
            'role' => DayCareMemberRole::Contributor
        ])->create();

        actingAs($contributor)
            ->patchJson(route('day-cares.update', $dayCare))
            ->assertForbidden()
            ->assertJsonMissingPath('data');

        assertEquals($name, $dayCare->refresh()->name);
    });

    test("users can't update day care", function () {
        $name = 'Original day care name';

        $dayCare = DayCare::factory()->create([
            'name' => $name
        ]);

        $user = User::factory()->create();

        actingAs($user)
            ->patchJson(route('day-cares.update', $dayCare))
            ->assertForbidden()
            ->assertJsonMissingPath('data');

        assertEquals($name, $dayCare->refresh()->name);
    });
});

describe('destroy', function () {
    test('super users can delete day care', function () {
        $dayCare = DayCare::factory()->create();

        $superUser = User::factory()->superUser()->create();

        actingAs($superUser)
            ->deleteJson(route('day-cares.destroy', $dayCare))
            ->assertNoContent();

        assertModelMissing($dayCare);
    });

    test("members can't delete day care", function () {
        $dayCare = DayCare::factory()->create();

        $administrator = User::factory()->hasAttached($dayCare, [
            'role' => DayCareMemberRole::Administrator
        ])->create();

        actingAs($administrator)
            ->deleteJson(route('day-cares.destroy', $dayCare))
            ->assertForbidden();

        assertModelExists($dayCare);
    });

    test("users can't delete day care", function () {
        $dayCare = DayCare::factory()->create();

        $user = User::factory()->create();

        actingAs($user)
            ->deleteJson(route('day-cares.destroy', $dayCare))
            ->assertForbidden();

        assertModelExists($dayCare);
    });
});
