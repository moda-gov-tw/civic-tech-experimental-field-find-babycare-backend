<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'relationship_with_infant' => fake()->text(20),
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'is_living_in_the_same_household' => false,
            'address_id' => Address::factory()
        ];
    }
}
