<?php

namespace App\Http\Resources\V1;

use App\Models\DayCareMember;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DayCareMemberResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->whenPivotLoaded(new DayCareMember(), function () {
                return $this->pivot->role;
            })
        ];
    }
}
