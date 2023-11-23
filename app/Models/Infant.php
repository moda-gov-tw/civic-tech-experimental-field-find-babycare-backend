<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Infant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id_number',
        'dob',
        'medical_conditions',
        'address_id'
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function applicationDrafts(): BelongsToMany
    {
        return $this->belongsToMany(ApplicationDraft::class)
            ->withTimestamps()
            ->using(ApplicationDraftInfant::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(InfantStatus::class);
    }
}
