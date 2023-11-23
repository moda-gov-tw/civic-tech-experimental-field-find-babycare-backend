<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function dayCares(): BelongsToMany
    {
        return $this->belongsToMany(DayCare::class)
            ->withTimestamps()
            ->using(ApplicationDraftDayCare::class);
    }

    public function infants(): BelongsToMany
    {
        return $this->belongsToMany(Infant::class)
            ->withTimestamps()
            ->using(ApplicationDraftInfant::class);
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class)
            ->withTimestamps()
            ->using(ApplicationDraftContact::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDraftDocument::class);
    }

    public function dayCareDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDraftDayCareDocument::class);
    }

    public function infantDocuments(): HasMany
    {
        return $this->hasMany(ApplicationDraftInfantDocument::class);
    }
}
