<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, LogsActivity, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'global_role',
        'is_2fa_enabled',
        'google2fa_secret',
        'is_active',
        'profile_data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_2fa_enabled' => 'boolean',
            'is_active' => 'boolean',
            'profile_data' => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'global_role', 'is_active'])
            ->logOnlyDirty();
    }

    // Relations
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class)
            ->withPivot(['role_at_school', 'meta', 'is_active'])
            ->withTimestamps();
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'teacher_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'teacher_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    public function disciplinaryActions(): HasMany
    {
        return $this->hasMany(DisciplinaryAction::class, 'teacher_id');
    }

    public function createdTemplates(): HasMany
    {
        return $this->hasMany(Template::class, 'author_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTeachers($query)
    {
        return $query->where('global_role', 'teacher');
    }

    public function scopeSuperAdmins($query)
    {
        return $query->where('global_role', 'super_admin');
    }

    // Méthodes utilitaires
    public function isSuperAdmin(): bool
    {
        return $this->global_role === 'super_admin';
    }

    public function isTeacher(): bool
    {
        return $this->global_role === 'teacher';
    }

    public function getSchoolRole(School $school): ?string
    {
        $pivot = $this->schools()->where('school_id', $school->id)->first();
        return $pivot?->pivot->role_at_school;
    }

    public function hasSchoolAccess(School $school): bool
    {
        return $this->schools()->where('school_id', $school->id)->where('is_active', true)->exists();
    }
}