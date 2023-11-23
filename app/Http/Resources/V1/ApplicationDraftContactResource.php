<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationDraftContactResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'relationship_with_infant' => $this->relationship_with_infant,
            'name' => $this->name,
            'phone' => $this->phone,
            'is_living_in_the_same_household' => $this->is_living_in_the_same_household,
            'address' => new AddressResource($this->whenLoaded('address'))
        ];
    }
}
