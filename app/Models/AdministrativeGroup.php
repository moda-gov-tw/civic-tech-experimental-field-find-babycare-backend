<?php

namespace App\Models;

use App\Enums\AdministrativeGroupMemberRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdministrativeGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps()
            ->using(AdministrativeGroupMember::class);
    }

    public function dayCares(): BelongsToMany
    {
        return $this->belongsToMany(DayCare::class)->withTimestamps();
    }
}
