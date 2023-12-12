<?php

namespace Database\Factories;

use App\Enums\InfantSex;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Infant>
 */
class InfantFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sex' => fake()->randomElement(InfantSex::values()),
            'name' => fake()->name(),
            'id_number' => fake()->uuid(),
            'dob' => fake()->dateTimeBetween('-1 day'),
            'medical_conditions' => fake()->text(200),
            'address_id' => Address::factory()
        ];
    }
}
