<?php

namespace Database\Factories;

use App\Enums\ApplicationDocumentType;
use App\Models\ApplicationDraft;
use Database\Factories\Helpers\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationDraftDocument>
 */
class ApplicationDraftDocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_draft_id' => ApplicationDraft::factory(),
            'type' => fake()->randomElement(ApplicationDocumentType::values()),
            'path' => Document::create()
        ];
    }
}
