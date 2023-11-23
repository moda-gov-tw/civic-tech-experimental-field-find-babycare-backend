<?php

namespace App\Models;

use App\Enums\AdministrativeGroupMemberRole;
use App\Enums\DayCareMemberRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isSuperUser(): bool
    {
        return $this->is_super_user;
    }

    public function administrativeGroups(): BelongsToMany
    {
        return $this->belongsToMany(AdministrativeGroup::class)
            ->withPivot('role')
            ->withTimestamps()
            ->using(AdministrativeGroupMember::class);
    }

    public function isInAdministrativeGroup(AdministrativeGroup $group, ?AdministrativeGroupMemberRole ...$roles): bool
    {
        $builder = $group->members()->wherePivot('user_id', $this->id);

        if (!empty($roles)) {
            $builder->wherePivotIn('role', $roles);
        }

        return $builder->first() !== null;
    }

    public function dayCares(): BelongsToMany
    {
        return $this->belongsToMany(DayCare::class)
            ->withPivot('role')
            ->withTimestamps()
            ->using(DayCareMember::class);
    }

    public function isInDayCare(DayCare $dayCare, ?DayCareMemberRole ...$roles): bool
    {
        $builder = $dayCare->members()->wherePivot('user_id', $this->id);

        if (!empty($roles)) {
            $builder->wherePivotIn('role', $roles);
        }

        return $builder->first() !== null;
    }

    public function applicationDrafts(): HasMany
    {
        return $this->hasMany(ApplicationDraft::class);
    }

    public function ownsApplicationDraft(ApplicationDraft $draft): bool
    {
        return $draft->user_id === $this->id;
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function ownsApplication(Application $application): bool
    {
        return $application->user_id === $this->id;
    }
}
