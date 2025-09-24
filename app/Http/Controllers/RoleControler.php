<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleControler extends Controller
{
    //
    public function index(){
        $roles = Roles::all();

        return response()->json(['roles' => $roles]);
    }

    public function store(Request $request){
        $validated = Validator::make($request->all(),[
            'rol' => 'required|string'
        ]);

        if($validated->fails()){
            return response()->json(['errors' => $validated->errors()],500);
        }

        $crearRol = Roles::create([
            'rol' => $request->rol
        ]);

        return response()->json(
            [
                'message' => 'Rol creado correctamente',
                'success' => true,
                'rol' => $crearRol
            ]);
    }

    public function update(Request $request, $id){

        $rol = Roles::find($id);

        if(!$rol){
            return response()->json(['message' => "No se ha encontrado el Rol"]);
        }

        $validated = Validator::make($request->all(),[
            'rol' => 'required|string'
        ]);

        if($validated->fails()){
            return response()->json(['errors' => $validated->errors()],500);
        }

        
        $rol->update($validated->validated());

        return response()->json(['message' => 'Actualizado correctamente', 'success' => true, 'rol' =>$rol]);
    }

    public function delete($id){
        $rol = Roles::find($id);

        if(!$rol){
            return response()->json(['message' => 'no se encontro el rol']);
        }

        $rol->delete();

        return response()->json(['message' => 'Rol elimminado correctamente']);
        
    }

    public function rolById($id){
        $rol = Roles::find($id);

        if(!$rol){
            return response()->json(['message' => 'no se encontro el rol']);
        }

        return response()->json(['rol' => $rol]);
    }
}
