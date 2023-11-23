<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'city' => fake()->city(),
            'district' => fake('en_US')->state(),
            'street' => fake()->streetAddress(),
        ];
    }
}
