<?php

// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Vérifie si l'utilisateur est connecté via le token JWT
        if (!auth('api')->check()) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $userRole = auth('api')->user()->role;

        // Vérifie si le rôle de l'utilisateur est dans la liste des rôles autorisés
        if (!in_array($userRole, $roles)) {
            return response()->json(['error' => 'Accès refusé. Permissions insuffisantes.'], 403);
        }

        return $next($request);
    }
}