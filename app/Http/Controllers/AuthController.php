<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

                $expectedRole = $guard === 'apiAdmin' ? 'admin' : ($guard === 'apiDoctor' ? 'doctor' : 'paciente');

                $userRole = $user->rol->rol ?? null;

                if ($userRole !== $expectedRole) {
                    // Role mismatch, continue to next guard
                    continue;
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
            $guards = ['apiAdmin', 'apiDoctor', 'apiPaciente'];

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    Auth::guard($guard)->logout();
                    break;
                }
            }

            return response()->json([
                'message' => 'Sesión cerrada correctamente',
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
