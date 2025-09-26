<?php

namespace App\Http\Controllers;

use App\Models\Consultorios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultoriosController extends Controller
{
    //
    public function index()
    {
        $consultorios = Consultorios::all();

        return response()->json(['consultorios' => $consultorios]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'codigo' => 'required|string',
            'ubicacion' => 'required|string',
            'piso' => 'required|numeric',
            'doctor_id' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }

        $crearConsultorio = Consultorios::create([
            'codigo' => $request->codigo,
            'ubicacion' => $request->ubicacion,
            'piso' => $request->piso,
            'doctor_id' => $request->doctor_id
        ]);

        return response()->json(
            [
                'message' => 'Consultorio creado correctamente',
                'success' => true,
                'rol' => $crearConsultorio
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $consultorio = Consultorios::find($id);

        if(!$consultorio){
            return response()->json(['message' => "No se ha encontrado el consultorio"]);
        }

        $validated = Validator::make($request->all(), [
            'codigo' => 'required|string',
            'ubicacion' => 'required|string',
            'piso' => 'required|numeric',
            'doctor_id' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }


        $consultorio->update($validated->validated());

        return response()->json(['message' => 'Actualizado correctamente', 'success' => true, 'consultorio' => $consultorio]);
    }

    public function delete($id)
    {
        $consultorio = Consultorios::find($id);

        if (!$consultorio) {
            return response()->json(['message' => 'no se encontro el consultorio']);
        }

        $consultorio->delete();

        return response()->json(['message' => 'consultorio eliminado correctamente']);
    }

    public function consultorioById($id)
    {
        $consultorio = Consultorios::find($id);

        if (!$consultorio) {
            return response()->json(['message' => 'no se encontro el consultorio']);
        }

        return response()->json(['consultorio' => $consultorio]);
    }


    /**
     * Obtener consultorio del doctor autenticado
     */
    public function miConsultorio(Request $request)
    {
        $doctor = auth('apiDoctor')->user() ?? $request->jwt_user;

        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        $consultorio = Consultorios::where('doctor_id', $doctor->id)->first();

        if (!$consultorio) {
            return response()->json(['message' => 'No tienes un consultorio asignado'], 404);
        }

        return response()->json(['mi_consultorio' => $consultorio]);
    }
}
