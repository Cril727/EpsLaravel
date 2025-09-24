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
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['message' => 'Usuario no autenticado'], 401);
            }

            // Load the role relationship
            $user->load('role');

            return response()->json([
                'user' => $user,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token inválido'], 401);
        }
    }

    public function actualizarPerfil(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['message' => 'Usuario no autenticado'], 401);
            }

        $validated = Validator::make($request->all(), [
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'telefono' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $updateData = [
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'success' => true,
            'user' => $user
        ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token inválido'], 401);
        }
    }

    public function actualizarPerfilAdmin(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validated = Validator::make($request->all(), [
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'telefono' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'rol_id' => 'required|numeric|exists:roles,id',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $updateData = [
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'rol_id' => $request->rol_id,
        ];

        // Only update password if provided
        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'success' => true,
            'user' => $user
        ]);
    }
}
