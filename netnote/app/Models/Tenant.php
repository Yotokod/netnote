<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $fillable = [
        'name',
        'domain',
        'database',
    ];

    public function getSchoolAttribute()
    {
        return School::where('subdomain', $this->domain)->first();
    }
}