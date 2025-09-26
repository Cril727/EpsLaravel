<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class MultiGuardJWTMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $guards = ['apiDoctor','apiPaciente','apiAdmin'];

        // Priorizar el guard del header si está presente
        $headerGuard = $request->header('X-User-Guard');
        if ($headerGuard && in_array($headerGuard, $guards)) {
            array_unshift($guards, $headerGuard);
            $guards = array_unique($guards);
        }

        foreach ($guards as $guard) {
            try {
                auth()->shouldUse($guard);                 // decirle a JWT qué guard usar
                $user = JWTAuth::parseToken()->authenticate();
                if ($user) {
                    // Dejar el usuario y el guard en el request para los middlewares siguientes
                    $request->merge([
                        'jwt_user'  => $user,
                        'jwt_guard' => $guard,
                    ]);
                    return $next($request);
                }
            } catch (\Throwable $e) {
                // probar el siguiente guard
            }
        }

        return response()->json(['Error' => 'Token inválido'], 401);
    }
}
