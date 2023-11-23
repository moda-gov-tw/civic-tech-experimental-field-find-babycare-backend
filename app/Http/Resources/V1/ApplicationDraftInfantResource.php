<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationDraftInfantResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'id_number' => $this->id_number,
            'dob' => $this->dob,
            'medical_conditions' => $this->medical_conditions,
            'statuses' => InfantStatusResource::collection($this->whenLoaded('statuses')),
            'address' => new AddressResource($this->whenLoaded('address')),
        ];
    }
}
