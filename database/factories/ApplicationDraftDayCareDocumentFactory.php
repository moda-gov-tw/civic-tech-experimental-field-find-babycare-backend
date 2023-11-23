<?php

namespace Database\Factories;

use App\Enums\ApplicationDayCareDocumentType;
use App\Models\ApplicationDraft;
use App\Models\DayCare;
use Database\Factories\Helpers\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\ApplicationDraftDayCareDocument>
 */
class ApplicationDraftDayCareDocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_draft_id' => ApplicationDraft::factory(),
            'day_care_id' => DayCare::factory(),
            'type' => fake()->randomElement(ApplicationDayCareDocumentType::cases()),
            'path' => Document::create()
        ];
    }
}
