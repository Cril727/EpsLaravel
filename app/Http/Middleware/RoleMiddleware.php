<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->jwt_user;

        if (!$user) {
            return response()->json(['Error' => 'Token inválido'], 401);
        }

        // Asegura que el modelo tenga la relación 'rol'
        if (method_exists($user, 'rol')) {
            $user->load('rol');
        }

        $userRole = $user->rol->rol ?? null;

        if (!$userRole || !in_array($userRole, $roles, true)) {
            return response()->json(['Error' => 'Error no tienes el permiso necesario'], 403);
        }

        return $next($request);
    }
}
