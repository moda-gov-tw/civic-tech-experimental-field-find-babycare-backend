<?php

namespace App\Models;

use App\Enums\DayCareMemberRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DayCareMember extends Pivot
{
    use HasFactory;

    protected $table = 'day_care_user';

    protected $casts = [
        'role' => DayCareMemberRole::class
    ];

    public function dayCare(): BelongsTo
    {
        return $this->belongsTo(DayCare::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
