<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationDraftResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'applicant' => new ApplicationDraftContactResource($this->whenLoaded('applicant')),
            'infants' => ApplicationDraftInfantResource::collection($this->whenLoaded('infants')),
            'contacts' => ApplicationDraftContactResource::collection($this->whenLoaded('contacts')),
        ];
    }
}
