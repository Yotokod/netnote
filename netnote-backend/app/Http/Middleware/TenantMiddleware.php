<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\School;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Si c'est un super admin, il peut accéder à toutes les écoles
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Pour les autres utilisateurs, vérifier qu'ils ont accès à une école
        $userSchools = $user->schools()->where('is_active', true)->get();
        
        if ($userSchools->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune école associée à ce compte'
            ], 403);
        }

        // Ajouter l'école active à la requête
        $activeSchool = $userSchools->first();
        $request->attributes->add(['current_school' => $activeSchool]);

        return $next($request);
    }
}