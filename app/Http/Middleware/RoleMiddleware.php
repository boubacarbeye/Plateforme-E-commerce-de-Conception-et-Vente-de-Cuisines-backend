<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Non authentifié.'
            ], 401);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Accès refusé.'
            ], 403);
        }

        return $next($request);
    }
}
