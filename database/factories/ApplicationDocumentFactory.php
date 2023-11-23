<?php

namespace Database\Factories;

use App\Enums\ApplicationDocumentType;
use App\Models\Application;
use Database\Factories\Helpers\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationDocument>
 */
class ApplicationDocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'type' => fake()->randomElement(ApplicationDocumentType::cases()),
            'path' => Document::create()
        ];
    }
}
