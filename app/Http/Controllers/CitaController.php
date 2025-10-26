<?php

namespace App\Http\Controllers;

use App\Models\CitasMedicas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AppointmentRequest;
use App\Services\ExpoPushNotificationService;

class CitaController extends Controller
{
    //
        public function index()
    {
        $citasMedicas = CitasMedicas::all();

        return response()->json(['citasMedicas' => $citasMedicas]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'fechaHora' => 'required|date',
            'estado'    => 'required|string',
            'novedad' => 'required|string',
            'paciente_id' => 'required|numeric',
            'doctor_id' => 'required|numeric',
            'consultorio_id' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }

        $crearCita = citasMedicas::create([
            'fechaHora' => $request->fechaHora,
            'estado' => $request->estado,
            'novedad' => $request->novedad,
            'paciente_id' => $request->paciente_id,
            'doctor_id' => $request->doctor_id,
            'consultorio_id' => $request->consultorio_id
        ]);

        // Send email to doctor for approval
        try {
            $doctor = $crearCita->doctor;
            Mail::to($doctor->email)->send(new AppointmentRequest($crearCita));
        } catch (\Exception $e) {
            // Log the error but don't fail the appointment creation
            Log::error('Failed to send appointment request email: ' . $e->getMessage());
        }

        // Send push notification to doctor
        try {
            $doctor = $crearCita->doctor;
            if ($doctor && $doctor->user && $doctor->user->expo_push_token) {
                $pushService = new ExpoPushNotificationService();
                $appointmentData = [
                    'id' => $crearCita->id,
                    'fechaHora' => $crearCita->fechaHora,
                    'doctor_name' => $doctor->nombre . ' ' . $doctor->apellido,
                    'consultorio_name' => $crearCita->consultorio->nombre ?? 'Consultorio',
                ];
                $pushService->sendAppointmentNotification($doctor->user->expo_push_token, $appointmentData);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send push notification: ' . $e->getMessage());
        }

        return response()->json(
            [
                'message' => 'Cita creado correctamente',
                'success' => true,
                'Cita' => $crearCita
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $Cita = citasMedicas::find($id);

        if (!$Cita) {
            return response()->json(['message' => "No se ha encontrado el Cita"]);
        }

        $oldStatus = $Cita->estado;

        $validated = Validator::make($request->all(), [
            'fechaHora' => 'required|date',
            'estado'    => 'required|string',
            'novedad' => 'required|string',
            'paciente_id' => 'required|numeric',
            'doctor_id' => 'required|numeric',
            'consultorio_id' => 'required|numeric',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 500);
        }

        $Cita->update($validated->validated());

        // Send push notification if status changed
        if ($oldStatus !== $Cita->estado) {
            try {
                $paciente = $Cita->paciente;
                if ($paciente && $paciente->user && $paciente->user->expo_push_token) {
                    $pushService = new ExpoPushNotificationService();
                    $appointmentData = [
                        'id' => $Cita->id,
                        'fechaHora' => $Cita->fechaHora,
                        'doctor_name' => $Cita->doctor->nombre . ' ' . $Cita->doctor->apellido,
                        'consultorio_name' => $Cita->consultorio->nombre ?? 'Consultorio',
                    ];
                    $pushService->sendAppointmentStatusUpdate(
                        $paciente->user->expo_push_token,
                        $Cita->estado,
                        $appointmentData
                    );
                }
            } catch (\Exception $e) {
                Log::error('Failed to send status update push notification: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Actualizado correctamente', 'success' => true, 'Cita' => $Cita]);
    }

    public function delete($id)
    {
        $Cita = citasMedicas::find($id);

        if (!$Cita) {
            return response()->json(['message' => 'no se encontro el Cita']);
        }

        $Cita->delete();

        return response()->json(['message' => 'Cita eliminado correctamente']);
    }

    public function CitaById($id)
    {
        $Cita = citasMedicas::find($id);

        if (!$Cita) {
            return response()->json(['message' => 'no se encontro el Cita']);
        }

        return response()->json(['Cita' => $Cita]);
    }

}
