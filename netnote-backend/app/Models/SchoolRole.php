<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolRole extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'can_assign_roles',
        'assignable_roles',
        'school_id',
        'is_system',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'assignable_roles' => 'array',
        'can_assign_roles' => 'boolean',
        'is_system' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Relation avec l'école
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relation avec les utilisateurs
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'school_user')
            ->withPivot('school_id', 'is_active')
            ->withTimestamps();
    }

    /**
     * Vérifier si le rôle a une permission spécifique
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return in_array($permission, $this->permissions ?? []) || 
               in_array('*', $this->permissions ?? []);
    }

    /**
     * Vérifier si le rôle peut assigner un autre rôle
     */
    public function canAssignRole(string $roleSlug): bool
    {
        if (!$this->can_assign_roles || !$this->is_active) {
            return false;
        }

        return in_array($roleSlug, $this->assignable_roles ?? []) ||
               in_array('*', $this->assignable_roles ?? []);
    }

    /**
     * Scope pour les rôles actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les rôles système
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope pour les rôles personnalisés
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }
}