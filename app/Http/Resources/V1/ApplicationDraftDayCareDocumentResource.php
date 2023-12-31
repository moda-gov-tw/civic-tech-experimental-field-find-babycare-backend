<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationDraftDayCareDocumentResource extends JsonResource
{
  /**
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'day_care_id' => $this->day_care_id,
      'type' => $this->type,
      'url' => route('application-drafts.day-care-documents.show', [$this->application_draft_id, $this->id])
    ];
  }
}
