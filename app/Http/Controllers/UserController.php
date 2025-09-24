<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
}
