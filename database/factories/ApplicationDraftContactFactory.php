<?php

namespace Database\Factories;

use App\Models\ApplicationDraft;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationDraftContact>
 */
class ApplicationDraftContactFactory extends Factory
{
  /**
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'application_draft_id' => ApplicationDraft::factory(),
      'contact_id' => Contact::factory(),
    ];
  }
}
