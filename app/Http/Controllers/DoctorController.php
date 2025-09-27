<?php

namespace App\Http\Controllers;

use App\Models\Doctores;
use App\Models\CitasMedicas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    //
    public function index()
    {
        $doctores = Doctores::all();

        return response()->json(['doctores' => $doctores]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'email' => 'required|email',
            'telefono' => 'required|string',
            'estado' => 'required|string',
            'rol_id' => 'required|numeric',
            'especialidad_id' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }

        $crearDoctor = doctores::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'estado'=> $request->estado,
            'password' => Hash::make('12345678'), // Default password for all doctors
            'rol_id' => $request->rol_id,
            'especialidad_id' => $request->especialidad_id,
        ]);

        return response()->json(
            [
                'message' => 'Doctor creado correctamente',
                'success' => true,
                'rol' => $crearDoctor
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $Doctor = doctores::find($id);

        if (!$Doctor) {
            return response()->json(['message' => "No se ha encontrado el Doctor"]);
        }

        $validated = Validator::make($request->all(), [
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'email' => 'required|email|unique:doctores,email,' . $Doctor->id,
            'telefono' => 'required|string',
            'estado' => 'required|in:Activo,Inactivo',
            'password' => 'sometimes|string',
            'rol_id' => 'required|numeric|exists:roles,id',
            'especialidad_id' => 'required|numeric|exists:especialidades,id',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $updateData = $validated->validated();

        // Hash password if provided
        if (isset($updateData['password']) && !empty($updateData['password'])) {
            $updateData['password'] = Hash::make($updateData['password']);
        } else {
            // Remove password from update data if not provided
            unset($updateData['password']);
        }

        $Doctor->update($updateData);

        return response()->json(['message' => 'Actualizado correctamente', 'success' => true, 'Doctor' => $Doctor]);
    }

    public function delete($id)
    {
        $Doctor = doctores::find($id);

        if (!$Doctor) {
            return response()->json(['message' => 'no se encontro el Doctor']);
        }

        $Doctor->delete();

        return response()->json(['message' => 'Doctor eliminado correctamente']);
    }

    public function DoctorById($id)
    {
        $Doctor = doctores::find($id);

        if (!$Doctor) {
            return response()->json(['message' => 'no se encontro el Doctor']);
        }

        return response()->json(['Doctor' => $Doctor]);
    }

    /**
     * Obtener perfil del doctor autenticado
     */
    public function miPerfil(Request $request)
    {
        $doctor = auth('apiDoctor')->user() ?? $request->jwt_user;

        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        return response()->json(['perfil' => $doctor->load('especialidad', 'rol')]);
    }

    /**
     * Actualizar perfil del doctor autenticado
     */
    public function actualizarPerfil(Request $request)
    {
        $doctor = auth('apiDoctor')->user() ?? $request->jwt_user;

        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        $validated = Validator::make($request->all(), [
            'nombres' => 'sometimes|string',
            'apellidos' => 'sometimes|string',
            'email' => 'sometimes|email|unique:doctores,email,' . $doctor->id,
            'telefono' => 'sometimes|string',
            'estado' => 'sometimes|string|in:Activo,Inactivo',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $doctor->update($validated->validated());
        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'success' => true,
            'perfil' => $doctor->load('especialidad', 'rol')
        ]);
    }

    /**
     * Obtener todas las citas del doctor autenticado
     */
    public function misCitas(Request $request)
    {
        $doctor = auth('apiDoctor')->user() ?? $request->jwt_user;

        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        $citas = CitasMedicas::where('doctor_id', $doctor->id)
            ->with(['paciente', 'consultorio'])
            ->orderBy('fechaHora', 'desc')
            ->get();

        return response()->json(['citas' => $citas]);
    }

    /**
     * Obtener citas pendientes del doctor autenticado
     */
    public function misCitasPendientes(Request $request)
    {
        $doctor = auth('apiDoctor')->user() ?? $request->jwt_user;

        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        $citas = CitasMedicas::where('doctor_id', $doctor->id)
            ->where('estado', 'Programada')
            ->with(['paciente', 'consultorio'])
            ->orderBy('fechaHora', 'asc')
            ->get();

        return response()->json(['citas_pendientes' => $citas]);
    }

    /**
     * Aprobar una cita (mantener estado como Programada)
     */
    public function aprobarCita(Request $request, $id)
    {
        $doctor = auth('apiDoctor')->user() ?? $request->jwt_user;

        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        $cita = CitasMedicas::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->where('estado', 'Programada')
            ->first();

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada o no se puede aprobar'], 404);
        }

        $cita->update(['estado' => 'Programada']);
        return response()->json([
            'message' => 'Cita aprobada correctamente',
            'success' => true,
            'cita' => $cita->load(['paciente', 'consultorio'])
        ]);
    }

    /**
     * Rechazar una cita (cambiar estado a Rechazada)
     */
    public function rechazarCita(Request $request, $id)
    {
        $doctor = auth('apiDoctor')->user() ?? $request->jwt_user;

        if (!$doctor || !isset($doctor->id)) {
            return response()->json(['error' => 'Usuario no válido'], 401);
        }

        $cita = CitasMedicas::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->where('estado', 'Programada')
            ->first();

        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada o no se puede rechazar'], 404);
        }

        $cita->update(['estado' => 'Rechazada']);
        return response()->json([
            'message' => 'Cita rechazada correctamente',
            'success' => true,
            'cita' => $cita->load(['paciente', 'consultorio'])
        ]);
    }
}
