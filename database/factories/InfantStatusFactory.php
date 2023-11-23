<?php

namespace Database\Factories;

use App\Enums\InfantStatusType;
use App\Models\Infant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InfantStatus>
 */
class InfantStatusFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'infant_id' => Infant::factory(),
            'type' => fake()->randomElement(InfantStatusType::cases())
        ];
    }
}
