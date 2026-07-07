<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. On récupère l'utilisateur connecté via le token JWT envoyé
        $user = auth('api')->user();

        // 2. Si le token est invalide ou que le rôle de l'utilisateur n'est pas autorisé
        if (! $user || ! in_array($user->role, $roles)) {
            return response()->json([
                'error' => 'Accès interdit. Permissions insuffisantes.',
            ], 403); // Code HTTP 403 = Interdit
        }

        // 3. Si tout est OK, on laisse la requête continuer son chemin vers le contrôleur
        return $next($request);
    }
}
