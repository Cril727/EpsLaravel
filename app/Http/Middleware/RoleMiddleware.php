<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $user->load('rol');

            $userRole = $user->rol ? $user->rol->rol : null;

            if (!$userRole || !in_array($userRole, $roles, true)) {
                return response()->json(['Error' => 'Error no tienes el permiso necesario'], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['Error' => 'Token inválido'], 401);
        }
    }
}
