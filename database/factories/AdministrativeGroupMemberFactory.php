<?php

namespace Database\Factories;

use App\Enums\AdministrativeGroupMemberRole;
use App\Models\AdministrativeGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdministrativeGroupMember>
 */
class AdministrativeGroupMemberFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'administrative_group_id' => AdministrativeGroup::factory(),
            'user_id' => User::factory(),
            'role' => AdministrativeGroupMemberRole::Contributor
        ];
    }

    public function administrator(): static
    {
        return $this->state(fn () => ['role' => AdministrativeGroupMemberRole::Administrator]);
    }
}
