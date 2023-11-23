<?php

namespace Database\Factories;

use App\Models\ApplicationDraft;
use App\Models\Infant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationDraftInfant>
 */
class ApplicationDraftInfantFactory extends Factory
{
  /**
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'application_draft_id' => ApplicationDraft::factory(),
      'infant_id' => Infant::factory(),
    ];
  }
}
