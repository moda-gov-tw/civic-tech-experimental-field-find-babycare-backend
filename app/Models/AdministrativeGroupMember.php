<?php

namespace App\Models;

use App\Enums\AdministrativeGroupMemberRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AdministrativeGroupMember extends Pivot
{
    use HasFactory;

    protected $table = 'administrative_group_user';

    protected $casts = [
        'role' => AdministrativeGroupMemberRole::class
    ];

    public function administrativeGroup(): BelongsTo
    {
        return $this->belongsTo(AdministrativeGroup::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
