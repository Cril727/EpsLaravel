<?php

namespace App\Http\Controllers;

use App\Models\Pacientes;
use App\Models\CitasMedicas;
use App\Models\Doctores;
use App\Models\Consultorios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PacienteController extends Controller
{
    /**
     * Public registration for patients
     */
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|unique:pacientes,email',
            'telefono' => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $paciente = Pacientes::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'rol_id' => 3, // Patient role
        ]);

        return response()->json([
            'message' => 'Paciente registrado correctamente',
            'success' => true,
            'paciente' => $paciente->load('rol')
        ], 201);
    }

    public function index()
    {
        $pacientes = Pacientes::all();

        return response()->json(['pacientes' => $pacientes]);
    }

    public function store(Request $request)
    {
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

        $crearPaciente = Pacientes::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id
        ]);

        return response()->json(
            [
                'message' => 'Usuario creado correctamente',
                'success' => true,
                'rol' => $crearPaciente
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $paciente = Pacientes::find($id);

        if(!$paciente){
            return response()->json(['message' => "No se ha encontrado el paciente"]);
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


        $paciente->update($validated->validated());

        return response()->json(['message' => 'Actualizado correctamente', 'success' => true, 'paciente' => $paciente]);
    }

    public function delete($id)
    {
        $paciente = Pacientes::find($id);

        if (!$paciente) {
            return response()->json(['message' => 'no se encontro el paciente']);
        }

        $paciente->delete();

        return response()->json(['message' => 'Paciente eliminado correctamente']);
    }

    public function pacienteById($id)
    {
        $paciente = Pacientes::find($id);

        if (!$paciente) {
            return response()->json(['message' => 'no se encontro el paciente']);
        }

        return response()->json(['paciente' => $paciente]);
    }

    // ========== MÉTODOS ESPECÍFICOS PARA PACIENTES ==========

    /**
     * Obtener perfil del paciente autenticado
     */
    public function miPerfil(Request $request)
    {
        $user = $request->jwt_user;
        $paciente = Pacientes::where('email', $user->email)->first();
        if (!$paciente) {
            $paciente = Pacientes::create([
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'password' => $user->password,
                'rol_id' => $user->rol_id,
            ]);
        }
        return response()->json(['perfil' => $paciente->load('rol')]);
    }

    /**
     * Actualizar perfil del paciente autenticado
     */
    public function actualizarPerfil(Request $request)
    {
        $user = $request->jwt_user;
        $paciente = Pacientes::where('email', $user->email)->first();
        if (!$paciente) {
            $paciente = Pacientes::create([
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'password' => $user->password,
                'rol_id' => $user->rol_id,
            ]);
        }

        $validated = Validator::make($request->all(), [
            'nombres' => 'sometimes|string',
            'apellidos' => 'sometimes|string',
            'email' => 'sometimes|email|unique:pacientes,email,' . $paciente->id,
            'telefono' => 'sometimes|string',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $paciente->update($validated->validated());
        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'success' => true,
            'perfil' => $paciente->load('rol')
        ]);
    }

    /**
     * Obtener todas las citas del paciente autenticado
     */
    public function misCitas(Request $request)
    {
        $user = $request->jwt_user;
        $paciente = Pacientes::where('email', $user->email)->first();
        if (!$paciente) {
            return response()->json(['citas' => []]);
        }
        $citas = CitasMedicas::where('paciente_id', $paciente->id)
            ->with(['doctor.especialidad', 'consultorio'])
            ->orderBy('fechaHora', 'desc')
            ->get();

        return response()->json(['citas' => $citas]);
    }

    /**
     * Solicitar una nueva cita
     */
    public function solicitarCita(Request $request)
    {
        $user = $request->jwt_user;

        // Find or create paciente based on user
        $paciente = Pacientes::where('email', $user->email)->first();
        if (!$paciente) {
            $paciente = Pacientes::create([
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'password' => $user->password,
                'rol_id' => $user->rol_id,
            ]);
        }

        $validated = Validator::make($request->all(), [
            'fechaHora' => 'required|date',
            'doctor_id' => 'required|exists:doctores,id',
            'consultorio_id' => 'required|exists:consultorios,id',
            'novedad' => 'sometimes|string',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        // Verificar que el consultorio pertenece al doctor
        $consultorio = \App\Models\Consultorios::where('id', $request->consultorio_id)
            ->where('doctor_id', $request->doctor_id)
            ->first();

        if (!$consultorio) {
            return response()->json(['message' => 'El consultorio no pertenece al doctor seleccionado'], 422);
        }

        // Verificar que no haya una cita en la misma fecha y hora
        $citaExistente = CitasMedicas::where('fechaHora', $request->fechaHora)
            ->where(function($query) use ($request) {
                $query->where('doctor_id', $request->doctor_id)
                      ->orWhere('consultorio_id', $request->consultorio_id);
            })
            ->exists();

        if ($citaExistente) {
            return response()->json(['message' => 'Ya existe una cita en esa fecha y hora'], 422);
        }

        $cita = CitasMedicas::create([
            'fechaHora' => $request->fechaHora,
            'estado' => 'Programada',
            'novedad' => $request->novedad ?? 'Cita solicitada por el paciente',
            'paciente_id' => $paciente->id,
            'doctor_id' => $request->doctor_id,
            'consultorio_id' => $request->consultorio_id,
        ]);

        return response()->json([
            'message' => 'Cita solicitada correctamente',
            'success' => true,
            'cita' => $cita->load(['doctor.especialidad', 'consultorio'])
        ]);
    }

    /**
     * Obtener lista de doctores disponibles
     */
    public function doctoresDisponibles(Request $request)
    {
        $doctores = Doctores::where('estado', 'Activo')
            ->with('especialidad')
            ->get();

        return response()->json(['doctores_disponibles' => $doctores]);
    }

    /**
     * Obtener horarios disponibles de un doctor
     */
    public function horariosDisponibles(Request $request, $doctor_id)
    {
        $horarios = \App\Models\Horarios::where('doctor_id', $doctor_id)
            ->where('estado', 'Activo')
            ->get();

        return response()->json(['horarios_disponibles' => $horarios]);
    }

    /**
     * Obtener consultorios disponibles de un doctor
     */
    public function consultoriosDisponibles(Request $request, $doctor_id)
    {
        $consultorios = Consultorios::where('doctor_id', $doctor_id)
            ->get();

        return response()->json(['consultorios_disponibles' => $consultorios]);
    }
}
