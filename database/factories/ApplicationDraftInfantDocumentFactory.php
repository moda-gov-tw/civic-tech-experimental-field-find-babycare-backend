<?php

namespace Database\Factories;

use App\Enums\ApplicationInfantDocumentType;
use App\Models\ApplicationDraft;
use App\Models\Infant;
use Database\Factories\Helpers\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationDraftInfantDocument>
 */
class ApplicationDraftInfantDocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_draft_id' => ApplicationDraft::factory(),
            'infant_id' => Infant::factory(),
            'type' => fake()->randomElement(ApplicationInfantDocumentType::values()),
            'path' => Document::create()
        ];
    }
}
