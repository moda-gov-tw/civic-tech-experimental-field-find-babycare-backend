<?php

namespace App\Models;

use App\Enums\InfantStatusType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfantStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'type'
    ];

    protected $casts = [
        'type' => InfantStatusType::class
    ];

    public function infant(): BelongsTo
    {
        return $this->belongsTo(Infant::class);
    }
}
