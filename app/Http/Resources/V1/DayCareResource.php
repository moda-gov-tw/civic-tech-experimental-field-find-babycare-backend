<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DayCareResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'is_in_construction' => $this->is_in_construction,
            'is_in_raffle' => $this->is_in_raffle,
            'is_accepting_applications' => $this->is_accepting_applications,
            'category' => $this->category,
            'operating_hours' => $this->operating_hours,
            'capacity' => $this->capacity,
            'monthly_fees' => $this->monthly_fees,
            'establishment_id' => $this->establishment_id,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'email' => $this->email,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'facebook_page_url'  => $this->facebook_page_url,
            'address' => new AddressResource($this->address)
        ];
    }
}
