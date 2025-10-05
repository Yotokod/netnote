<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier si l'utilisateur a l'un des rôles requis
        if (!in_array($user->global_role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé. Rôle requis: ' . implode(', ', $roles)
            ], 403);
        }

        return $next($request);
    }
}