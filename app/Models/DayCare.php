<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DayCare extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'is_in_construction',
        'is_in_raffle',
        'is_accepting_applications',
        'category',
        'operating_hours',
        'capacity',
        'monthly_fees',
        'establishment_id',
        'phone',
        'fax',
        'email',
        'lat',
        'lon',
        'facebook_page_url',
        'address_id'
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function administrativeGroups(): BelongsToMany
    {
        return $this->belongsToMany(AdministrativeGroup::class)->withTimestamps();
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps()
            ->using(DayCareMember::class);
    }

    public function applicationDrafts(): BelongsToMany
    {
        return $this->belongsToMany(ApplicationDraft::class)
            ->withTimestamps()
            ->using(ApplicationDraftDayCare::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function isInRaffle(): bool
    {
        return $this->is_in_raffle;
    }
}
