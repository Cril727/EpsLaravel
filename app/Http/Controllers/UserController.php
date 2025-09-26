<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    //
    public function index()
    {
        $users = User::all();

        return response()->json(['users' => $users]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'telefono' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            'rol_id' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }

        $crearUser = User::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id
        ]);

        return response()->json(
            [
                'message' => 'Usuario creado correctamente',
                'success' => true,
                'rol' => $crearUser
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => "No se ha encontrado el usuario"]);
        }

        $validated = Validator::make($request->all(), [
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'email' => 'required|email',
            'telefono' => 'required|string',
            'password' => 'required|string',
            'rol_id' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }


        $user->update($validated->validated());

        return response()->json(['message' => 'Actualizado correctamente', 'success' => true, 'user' => $user]);
    }

    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'no se encontro el usuario']);
        }

        $user->delete();

        return response()->json(['message' => 'usuario elimminado correctamente']);
    }

    public function userById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'no se encontro el Usuario']);
        }

        return response()->json(['user' => $user]);
    }

    public function miPerfil(Request $request)
    {
        try {
            $user = auth('apiAdmin')->user() ?? $request->jwt_user;

            if (!$user || !isset($user->id)) {
                return response()->json(['error' => 'Usuario no válido'], 401);
            }

            // Load the role relationship - use 'rol' instead of 'role'
            $user->load('rol');

            return response()->json([
                'user' => $user,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token inválido',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function actualizarPerfil(Request $request)
    {
        try {
            $user = auth('apiAdmin')->user() ?? $request->jwt_user;

            if (!$user || !isset($user->id)) {
                return response()->json(['error' => 'Usuario no válido'], 401);
            }

            // Determine the table name for email uniqueness validation
            $tableName = $this->getTableName($user);

            $validated = Validator::make($request->all(), [
                'nombres' => 'required|string',
                'apellidos' => 'required|string',
                'telefono' => 'sometimes|string',
                'email' => 'required|email|unique:' . $tableName . ',email,' . $user->id,
                'password' => 'nullable|string|min:6',
            ]);

            if ($validated->fails()) {
                return response()->json(['errors' => $validated->errors()], 422);
            }

            $updateData = [
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
            ];

            // Add telefono if provided
            if ($request->has('telefono')) {
                $updateData['telefono'] = $request->telefono;
            }

            // Only update password if provided
            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Reload the user with relationships
            $user->load('rol');

            return response()->json([
                'message' => 'Perfil actualizado correctamente',
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getTableName($user)
    {
        $className = get_class($user);
        switch ($className) {
            case 'App\Models\User':
                return 'users';
            case 'App\Models\Doctores':
                return 'doctores';
            case 'App\Models\Pacientes':
                return 'pacientes';
            default:
                return 'users';
        }
    }

    public function actualizarPerfilAdmin(Request $request, $id)
    {
        // First try to find in User table
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Get the current role to determine which table to validate against
        $currentRole = $user->rol_id;

        // Determine table name for email uniqueness validation
        $tableName = 'users'; // Default to users table

        $validated = Validator::make($request->all(), [
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'telefono' => 'sometimes|string',
            'email' => 'required|email|unique:' . $tableName . ',email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'rol_id' => 'required|numeric|exists:roles,id',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $updateData = [
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'rol_id' => $request->rol_id,
        ];

        // Add telefono if provided
        if ($request->has('telefono')) {
            $updateData['telefono'] = $request->telefono;
        }

        // Only update password if provided and not empty
        if ($request->password && !empty($request->password)) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Reload the user with relationships
        $user->load('rol');

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'success' => true,
            'user' => $user
        ]);
    }
}
