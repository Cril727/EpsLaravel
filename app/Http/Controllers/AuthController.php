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

                $role = $user->role
                    ?? $user->rol
                    ?? ($user->rol_id ?? null)
                    ?? ($guard === 'apiAdmin' ? 'admin' : ($guard === 'apiDoctor' ? 'doctor' : 'paciente'));

                return response()->json([
                    'access_token' => $token,
                    'guard'        => $guard,
                    'user'         => $user,
                ]);
            }
        }

        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }
}
