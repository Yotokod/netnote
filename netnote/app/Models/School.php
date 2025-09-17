<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class School extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'custom_domain',
        'logo_path',
        'founder',
        'year_founded',
        'country_id',
        'city_id',
        'quartier',
        'phones',
        'email_pro',
        'about',
        'bibliography',
        'settings',
        'template_id',
        'bulletin_template_id',
        'is_active',
    ];

    protected $casts = [
        'phones' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'year_founded' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'is_active', 'settings'])
            ->logOnlyDirty();
    }

    // Relations
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function bulletinTemplate(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'bulletin_template_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role_at_school', 'meta', 'is_active'])
            ->withTimestamps();
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
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

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function series(): HasMany
    {
        return $this->hasMany(Serie::class);
    }

    public function assessmentTypes(): HasMany
    {
        return $this->hasMany(AssessmentType::class);
    }

    public function disciplinaryActions(): HasMany
    {
        return $this->hasMany(DisciplinaryAction::class);
    }

    // Scopes et méthodes utilitaires
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullNameAttribute(): string
    {
        return $this->name . ' (' . $this->city->name . ')';
    }
}
