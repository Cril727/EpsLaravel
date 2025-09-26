<?php

namespace App\Http\Controllers;

use App\Models\Horarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HorarioController extends Controller
{
    //
    public function index()
    {
        $horarios = Horarios::all();

        return response()->json(['horarios' => $horarios]);
    }

    public function store(Request $request)
    {
        $doctor = auth('apiDoctor')->user() ?? $request->jwt_user;

        // Ensure we have a valid doctor user
        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        $validated = Validator::make($request->all(), [
            'horaInicio' => 'required|date_format:H:i',
            'horaFin'    => 'required|date_format:H:i',
            'estado' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $crearHorario = Horarios::create([
            'horaInicio' => $request->horaInicio,
            'horaFin' => $request->horaFin,
            'estado' => $request->estado,
            'doctor_id' => $doctor->id
        ]);

        return response()->json(
            [
                'message' => 'horario creado correctamente',
                'success' => true,
                'horario' => $crearHorario
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $doctor = auth('apiDoctor')->user() ?? $request->jwt_user;

        // Ensure we have a valid doctor user
        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        $horario = Horarios::where('id', $id)->where('doctor_id', $doctor->id)->first();

        if (!$horario) {
            return response()->json(['message' => "No se ha encontrado el horario o no tienes permisos para editarlo"]);
        }

        $validated = Validator::make($request->all(), [
            'horaInicio' => 'required|date_format:H:i',
            'horaFin'    => 'required|date_format:H:i',
            'estado' => 'required|string',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $horario->update($validated->validated());

        return response()->json(['message' => 'Actualizado correctamente', 'success' => true, 'horario' => $horario]);
    }

    public function delete($id)
    {
        $doctor = auth('apiDoctor')->user() ?? request()->jwt_user;

        // Ensure we have a valid doctor user
        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        $horario = Horarios::where('id', $id)->where('doctor_id', $doctor->id)->first();

        if (!$horario) {
            return response()->json(['message' => 'no se encontro el horario o no tienes permisos para eliminarlo']);
        }

        $horario->delete();

        return response()->json(['message' => 'horario eliminado correctamente']);
    }

    public function horarioById($id)
    {
        $horario = Horarios::find($id);

        if (!$horario) {
            return response()->json(['message' => 'no se encontro el horario']);
        }

        return response()->json(['horario' => $horario]);
    }
    
    /**
     * Obtener horarios del doctor autenticado
     */
    public function misHorarios(Request $request)
    {
        // Use auth() helper as primary method, fallback to middleware
        $doctor = auth('apiDoctor')->user() ?? $request->jwt_user;

        // Ensure we have a valid doctor user
        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        $horarios = Horarios::where('doctor_id', $doctor->id)
            ->orderBy('horaInicio', 'asc')
            ->get();

        return response()->json(['horarios' => $horarios]);
    }
}
