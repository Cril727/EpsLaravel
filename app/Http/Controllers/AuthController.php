<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($v->fails()) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors'  => $v->errors(),
            ], 422);
        }

        $credentials = $v->validated();

        $guards = ['apiAdmin', 'apiDoctor', 'apiPaciente'];

        foreach ($guards as $guard) {
            if ($token = Auth::guard($guard)->attempt($credentials)) {
                $user = Auth::guard($guard)->user();
                
                if ($user && $user->rol_id) {
                    $role = \App\Models\Roles::find($user->rol_id);
                    if ($role) {
                        $user->rol = $role;
                    }
                }

                return response()->json([
                    'access_token' => $token,
                    'guard'        => $guard,
                    'user'         => $user,
                ]);
            }
        }

        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            
            return response()->json([
                'message' => 'Sesión cerrada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
