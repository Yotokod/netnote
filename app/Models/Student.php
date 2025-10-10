<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Student extends Model
{
    use LogsActivity;

    protected $fillable = [
        'first_name',
        'last_name',
        'matricule',
        'date_of_birth',
        'birth_place',
        'gender',
        'blood_group',
        'medical_info',
        'documents',
        'photo_path',
        'languages',
        'school_id',
        'nationality_id',
        'religion_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'medical_info' => 'array',
        'documents' => 'array',
        'languages' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'gender'])
            ->logOnlyDirty();
    }

    // Relations
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'student_parents')
            ->withPivot(['relationship_type', 'is_primary_contact', 'can_pick_up', 'emergency_contact'])
            ->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function disciplinaryActions(): HasMany
    {
        return $this->hasMany(DisciplinaryAction::class);
    }

    // Méthodes utilitaires
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->diffInYears(now());
    }

    public function getCurrentEnrollment()
    {
        return $this->enrollments()
            ->where('status', 'active')
            ->whereHas('academicYear', function ($query) {
                $query->where('is_active', true);
            })
            ->first();
    }
}
