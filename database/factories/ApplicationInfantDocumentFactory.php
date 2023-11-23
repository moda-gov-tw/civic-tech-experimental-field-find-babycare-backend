<?php

namespace Database\Factories;

use App\Enums\ApplicationInfantDocumentType;
use App\Models\Application;
use Database\Factories\Helpers\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationInfantDocument>
 */
class ApplicationInfantDocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'type' => fake()->randomElement(ApplicationInfantDocumentType::cases()),
            'path' => Document::create()
        ];
    }
}
