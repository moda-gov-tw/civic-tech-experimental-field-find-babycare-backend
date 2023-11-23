<?php

namespace Database\Factories;

use App\Enums\DayCareMemberRole;
use App\Models\DayCare;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DayCareMember>
 */
class DayCareMemberFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'day_care_id' => DayCare::factory(),
            'user_id' => User::factory(),
            'role' => DayCareMemberRole::Contributor
        ];
    }

    public function administrator(): static
    {
        return $this->state(fn () => ['role' => DayCareMemberRole::Administrator]);
    }
}
