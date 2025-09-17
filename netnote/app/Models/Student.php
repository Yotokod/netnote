<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Student extends Model
{
    use LogsActivity;

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'birth_place',
        'nationality',
        'gender',
        'blood_group',
        'medical_info',
        'documents',
        'photo_path',
        'parent_info',
        'languages',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'medical_info' => 'array',
        'documents' => 'array',
        'parent_info' => 'array',
        'languages' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'gender'])
            ->logOnlyDirty();
    }

    // Relations
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
