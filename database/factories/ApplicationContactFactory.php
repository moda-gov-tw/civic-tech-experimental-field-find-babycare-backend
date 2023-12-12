<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationContact>
 */
class ApplicationContactFactory extends Factory
{
  /**
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'application_id' => Application::factory(),
      'contact_id' => Contact::factory(),
    ];
  }
}
