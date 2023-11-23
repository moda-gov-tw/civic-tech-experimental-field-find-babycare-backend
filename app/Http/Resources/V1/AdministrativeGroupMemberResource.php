<?php

namespace App\Http\Resources\V1;

use App\Models\AdministrativeGroupMember;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdministrativeGroupMemberResource extends JsonResource
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
            'role' => $this->whenPivotLoaded(new AdministrativeGroupMember(), function () {
                return $this->pivot->role;
            })
        ];
    }
}
