<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use App\Mail\PasswordResetMail;
use App\Models\Doctores;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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

    public function resetPassword(Request $request)
    {
        // Debug: Log the request data
        Log::info('Reset password request data', ['data' => $request->all(), 'content' => $request->getContent()]);

        $v = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($v->fails()) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors' => $v->errors(),
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            $paciente = Pacientes::where('email', $request->email)->first();
            $doctor = Doctores::where('email', $request->email)->first();

            // Find the user in any of the three roles
            $foundUser = null;
            if ($user) {
                $foundUser = $user;
            } elseif ($paciente) {
                $foundUser = $paciente;
            } elseif ($doctor) {
                $foundUser = $doctor;
            }

            if (!$foundUser) {
                return response()->json([
                    'message' => 'Usuario no encontrado',
                    'errors' => ['email' => ['El correo electrónico no está registrado']]
                ], 422);
            }

            // Generate a random temporary password (8 characters)
            $temporaryPassword = Str::random(8);

            // Hash the temporary password and update the found user
            $foundUser->password = Hash::make($temporaryPassword);
            $foundUser->save();

            // Send email with temporary password
            try {
                Mail::to($foundUser->email)->send(new PasswordResetMail($temporaryPassword));

                return response()->json([
                    'message' => 'Se ha enviado una contraseña temporal a tu correo electrónico.',
                    'success' => true
                ]);
            } catch (\Exception $mailException) {
                // Log the email error but still return success since password was updated
                Log::error('Failed to send password reset email', [
                    'email' => $foundUser->email,
                    'error' => $mailException->getMessage()
                ]);

                return response()->json([
                    'message' => 'Tu contraseña ha sido restablecida. La contraseña temporal es: ' . $temporaryPassword,
                    'success' => true,
                    'temporary_password' => $temporaryPassword
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud de restablecimiento de contraseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        // Validate the request
        $v = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/|confirmed',
            'password_confirmation' => 'required|string',
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La nueva contraseña debe contener al menos una mayúscula, una minúscula, un número y un símbolo especial.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password_confirmation.required' => 'La confirmación de la contraseña es obligatoria.',
        ]);

        if ($v->fails()) {
            return response()->json([
                'message' => 'Errores de validación',
                'errors' => $v->errors(),
            ], 422);
        }

        try {
            // Get the authenticated user from any guard
            $guards = ['apiAdmin', 'apiDoctor', 'apiPaciente'];
            $user = null;

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    $user = Auth::guard($guard)->user();
                    break;
                }
            }

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no autenticado',
                ], 401);
            }

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'La contraseña actual es incorrecta',
                    'errors' => ['current_password' => ['La contraseña actual es incorrecta']]
                ], 422);
            }

            // Check if new password is different from current
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'La nueva contraseña debe ser diferente a la actual',
                    'errors' => ['password' => ['La nueva contraseña debe ser diferente a la actual']]
                ], 422);
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'message' => 'Contraseña actualizada exitosamente',
                'success' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cambiar la contraseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
