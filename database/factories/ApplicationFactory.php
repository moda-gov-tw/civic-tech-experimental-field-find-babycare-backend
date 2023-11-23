<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\DayCare;
use App\Models\Infant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'day_care_id' => DayCare::factory(),
            'infant_id' => Infant::factory(),
            'status' => ApplicationStatus::Submitted
        ];
    }
}
