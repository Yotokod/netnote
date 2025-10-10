<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Plan extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'price',
        'billing_cycle',
        'max_students',
        'max_teachers',
        'max_storage_gb',
        'has_custom_domain',
        'has_advanced_reports',
        'has_api_access',
        'has_priority_support',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'has_custom_domain' => 'boolean',
        'has_advanced_reports' => 'boolean',
        'has_api_access' => 'boolean',
        'has_priority_support' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'price', 'is_active'])
            ->logOnlyDirty();
    }

    // Relations
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class)
            ->withPivot(['value'])
            ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Méthodes utilitaires
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', ' ') . ' FCFA';
    }

    public function hasFeature(string $featureSlug): bool
    {
        return $this->features()->where('slug', $featureSlug)->exists();
    }
}
