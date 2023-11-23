<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => ApplicationStatus::class
    ];

    protected $fillable = [
        'day_care_id',
        'infant_id',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dayCare(): BelongsTo
    {
        return $this->belongsTo(DayCare::class);
    }

    public function infant(): BelongsTo
    {
        return $this->belongsTo(Infant::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function dayCareDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDayCareDocument::class);
    }

    public function infantDocuments(): HasMany
    {
        return $this->hasMany(ApplicationInfantDocument::class);
    }
}
