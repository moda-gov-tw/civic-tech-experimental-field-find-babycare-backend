<?php

namespace Database\Factories;

use App\Models\ApplicationDraft;
use App\Models\DayCare;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationDraftDayCare>
 */
class ApplicationDraftDayCareFactory extends Factory
{
  /**
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'application_draft_id' => ApplicationDraft::factory(),
      'day_care_id' => DayCare::factory(),
    ];
  }
}
