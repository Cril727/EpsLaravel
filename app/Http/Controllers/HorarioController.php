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
        $validated = Validator::make($request->all(), [
            'horaInicio' => 'required|date_format:H:i',
            'horaFin'    => 'required|date_format:H:i',
            'estado' => 'required|string',
            'doctor_id' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }

        $crearHorario = Horarios::create([
            'horaInicio' => $request->horaInicio,
            'horaFin' => $request->horaFin,
            'estado' => $request->estado,
            'doctor_id' => $request->doctor_id
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
        $horario = Horarios::find($id);

        if (!$horario) {
            return response()->json(['message' => "No se ha encontrado el horario"]);
        }

        $validated = Validator::make($request->all(), [
            'horaInicio' => 'required|date_format:H:i',
            'horaFin'    => 'required|date_format:H:i',
            'estado' => 'required|string',
            'doctor_id' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }


        $horario->update($validated->validated());

        return response()->json(['message' => 'Actualizado correctamente', 'success' => true, 'horario' => $horario]);
    }

    public function delete($id)
    {
        $horario = Horarios::find($id);

        if (!$horario) {
            return response()->json(['message' => 'no se encontro el horario']);
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

    // ========== MÉTODOS ESPECÍFICOS PARA DOCTORES ==========

    /**
     * Obtener horarios del doctor autenticado
     */
    public function misHorarios()
    {
        $doctor = auth('apiDoctor')->user();
        $horarios = Horarios::where('doctor_id', $doctor->id)
            ->orderBy('horaInicio', 'asc')
            ->get();

        return response()->json(['mis_horarios' => $horarios]);
    }
}
