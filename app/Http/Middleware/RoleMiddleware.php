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
    public function handle(Request $request, Closure $next,...$rol): Response
    {
        $user = JWTAuth::parseToken()->authenticate();

        if(!in_array($user->$rol,$rol,true)){
            return response()->json(['Error' => 'Error no tienes el permiso necesario'],403);
        }
        return $next($request);
    }
}
