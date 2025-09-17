<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'flag_path',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }
}