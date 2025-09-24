<?php

namespace App\Http\Controllers;

use App\Models\Especialidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EspecialidadController extends Controller
{
    //
        public function index()
    {
        $especialidad = Especialidades::all();

        return response()->json(['especialidad' => $especialidad]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'especialidad' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }

        $crearEspecialidad = Especialidades::create([
            'especialidad' => $request->especialidad,
        ]);

        return response()->json(
            [
                'message' => 'especialidad creado correctamente',
                'success' => true,
                'rol' => $crearEspecialidad
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $especialidad = Especialidades::find($id);

        if(!$especialidad){
            return response()->json(['message' => "No se ha encontrado la especialidad"]);
        }

        $validated = Validator::make($request->all(), [
            'especialidad' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }


        $especialidad->update($validated->validated());

        return response()->json(['message' => 'Actualizado correctamente', 'success' => true, 'especialidad' => $especialidad]);
    }

    public function delete($id)
    {
        $especialidad = Especialidades::find($id);

        if (!$especialidad) {
            return response()->json(['message' => 'no se encontro el especialidad']);
        }

        $especialidad->delete();

        return response()->json(['message' => 'especialidad eliminado correctamente']);
    }

    public function especialidadById($id)
    {
        $especialidad = Especialidades::find($id);

        if (!$especialidad) {
            return response()->json(['message' => 'no se encontro el especialidad']);
        }

        return response()->json(['especialidad' => $especialidad]);
    }
}
