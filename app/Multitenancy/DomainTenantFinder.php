<?php

namespace App\Multitenancy;

use App\Models\School;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class DomainTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?IsTenant
    {
        $host = $request->getHost();
        
        // Si c'est le domaine principal, pas de tenant
        if ($host === config('app.domain', 'netnote.local')) {
            return null;
        }

        // Extraire le sous-domaine
        $subdomain = $this->extractSubdomain($host);
        
        if (!$subdomain) {
            return null;
        }

        // Chercher l'école par sous-domaine ou domaine custom
        $school = School::where('subdomain', $subdomain)
            ->orWhere('custom_domain', $host)
            ->where('is_active', true)
            ->first();

        if (!$school) {
            return null;
        }

        // Créer ou récupérer le tenant
        return Tenant::firstOrCreate([
            'name' => $school->name,
            'domain' => $subdomain,
        ]);
    }

    protected function extractSubdomain(string $host): ?string
    {
        $baseDomain = config('app.domain', 'netnote.local');
        
        // Si c'est un domaine custom complet, on le retourne tel quel
        if (!str_contains($host, $baseDomain)) {
            return $host;
        }

        // Extraire le sous-domaine
        $subdomain = str_replace('.' . $baseDomain, '', $host);
        
        return $subdomain !== $host ? $subdomain : null;
    }
}