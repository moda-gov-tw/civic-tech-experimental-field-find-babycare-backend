<?php

namespace Database\Factories;

use App\Models\AdministrativeGroup;
use App\Models\DayCare;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdministrativeGroupDayCare>
 */
class AdministrativeGroupDayCareFactory extends Factory
{
  /*
     * @return array<string, mixed>
     */
  public function definition(): array
  {
    return [
      'administrative_group_id' => AdministrativeGroup::factory(),
      'day_care_id' => DayCare::factory()
    ];
  }
}
