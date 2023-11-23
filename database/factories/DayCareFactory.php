<?php

namespace Database\Factories;

use App\Enums\DayCareCategory;
use App\Enums\DayCareType;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DayCare>
 */
class DayCareFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => DayCareType::Public,
            'is_in_construction' => false,
            'is_in_raffle' => false,
            'is_accepting_applications' => true,
            'category' => DayCareCategory::Center,
            'operating_hours' => fake()->text(20),
            'capacity' => fake()->numberBetween(1, 100),
            'monthly_fees' => fake()->randomFloat(1, 10000),
            'establishment_id' => fake()->uuid(),
            'phone' => fake()->phoneNumber(),
            'fax' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'lat' => 0,
            'lon' => 0,
            'facebook_page_url' => fake()->url(),
            'address_id' => Address::factory()
        ];
    }

    public function semiPublic(): static
    {
        return $this->state(fn () => ['type' => DayCareType::SemiPublic]);
    }

    public function private(): static
    {
        return $this->state(fn () => ['type' => DayCareType::Private]);
    }

    public function inConstruction(): static
    {
        return $this->state(fn () => ['is_in_construction' => true]);
    }

    public function inRaffle(): static
    {
        return $this->state(fn () => ['is_in_raffle' => true]);
    }

    public function closed(): static
    {
        return $this->state(fn () => ['is_accepting_applications' => false]);
    }

    public function home(): static
    {
        return $this->state(fn () => ['category' => DayCareCategory::Home]);
    }
}
