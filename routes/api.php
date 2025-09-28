<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ConsultoriosController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\RoleControler;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [PacienteController::class, 'register']); // Public registration for patients

// ===== JWT PROTEGIDAS ===== //
Route::middleware(['jwt.multiguard'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/addPaciete', [PacienteController::class, 'store']);


    // ===== DOCTOR =====
    Route::middleware(['rol:doctor'])->group(function () {
        // Perfil del doctor
        Route::get('/mi-perfil', [DoctorController::class, 'miPerfil']);
        Route::put('/actualizar-perfil', [DoctorController::class, 'actualizarPerfil']);

        // Citas del doctor
        Route::get('/doctor/mis-citas', [DoctorController::class, 'misCitas']);
        Route::get('/doctor/mis-citas-pendientes', [DoctorController::class, 'misCitasPendientes']);
        Route::put('/doctor/aprobar-cita/{id}', [DoctorController::class, 'aprobarCita']);
        Route::put('/doctor/rechazar-cita/{id}', [DoctorController::class, 'rechazarCita']);
        Route::put('/doctor/completar-cita/{id}', [DoctorController::class, 'completarCita']);

        // Horarios del doctor
        Route::get('/doctor/mis-horarios', [HorarioController::class, 'misHorarios']);
        Route::post('/doctor/addHorario', [HorarioController::class, 'store']);
        Route::put('/doctor/updateHorario/{id}', [HorarioController::class, 'update']);
        Route::delete('/doctor/deleteHorario/{id}', [HorarioController::class, 'delete']);
        Route::get('/doctor/horarioById/{id}', [HorarioController::class, 'horarioById']);

        // Consultorio del doctor
        Route::get('/doctor/mi-consultorio', [ConsultoriosController::class, 'miConsultorio']);
    });

    // ===== PACIENTE =====
    Route::middleware(['rol:paciente'])->group(function () {
        // Perfil del paciente
        Route::get('/mi-perfil', [PacienteController::class, 'miPerfil']);
        Route::put('/actualizar-perfil', [PacienteController::class, 'actualizarPerfil']);

        // Citas del paciente
        Route::get('/mis-citas', [PacienteController::class, 'misCitas']);
        Route::post('/solicitar-cita', [PacienteController::class, 'solicitarCita']);
        Route::get('/doctores-disponibles', [PacienteController::class, 'doctoresDisponibles']);
        Route::get('/horarios-disponibles/{doctor_id}', [PacienteController::class, 'horariosDisponibles']);
        Route::get('/consultorios-disponibles/{doctor_id}', [PacienteController::class, 'consultoriosDisponibles']);
    });

    // ===== ADMIN =====
    Route::middleware(['rol:admin'])->group(function () {
        // Perfil del admin
        Route::get('/mi-perfil', [UserController::class, 'miPerfil']);
        Route::put('/actualizar-perfil', [UserController::class, 'actualizarPerfil']);

        // Roles
        Route::post('/addRol', [RoleControler::class, 'store']);
        Route::get('/roles', [RoleControler::class, 'index']);
        Route::put('/updateRoles/{id}', [RoleControler::class, 'update']);
        Route::delete('/deleteRol/{id}', [RoleControler::class, 'delete']);
        Route::get('/rolById/{id}', [RoleControler::class, 'rolById']);

        // Users (admins)
        Route::post('/addUser', [UserController::class, 'store']);
        Route::get('/users', [UserController::class, 'index']);
        Route::put('/updateUser/{id}', [UserController::class, 'update']);
        Route::put('/actualizar-perfil-admin/{id}', [UserController::class, 'actualizarPerfilAdmin']);
        Route::delete('/deleteUser/{id}', [UserController::class, 'delete']);
        Route::get('/userById/{id}', [UserController::class, 'userById']);

        // Doctores (gestión)
        Route::post('/addDoctor', [DoctorController::class, 'store']);
        Route::get('/doctores', [DoctorController::class, 'index']);
        Route::put('/updateDoctor/{id}', [DoctorController::class, 'update']);
        Route::delete('/deleteDoctor/{id}', [DoctorController::class, 'delete']);
        Route::get('/DoctorById/{id}', [DoctorController::class, 'DoctorById']);

        // Especialidades (gestión)
        Route::post('/addEspecialidad', [EspecialidadController::class, 'store']);
        Route::get('/Especialidades', [EspecialidadController::class, 'index']);
        Route::put('/updateEspecialidad/{id}', [EspecialidadController::class, 'update']);
        Route::delete('/deleteEspecialidad/{id}', [EspecialidadController::class, 'delete']);
        Route::get('/especialidadById/{id}', [EspecialidadController::class, 'especialidadById']);

        // Pacientes (gestión)
        Route::post('/addPaciete', [PacienteController::class, 'store']);
        Route::get('/pacientes', [PacienteController::class, 'index']);
        Route::put('/updatePaciente/{id}', [PacienteController::class, 'update']);
        Route::delete('/deletePaciente/{id}', [PacienteController::class, 'delete']);
        Route::get('/pacienteById/{id}', [PacienteController::class, 'pacienteById']);

        // Consultorios (gestión)
        Route::post('/addConsultorio', [ConsultoriosController::class, 'store']);
        Route::get('/consultorios', [ConsultoriosController::class, 'index']);
        Route::put('/updateConsultorio/{id}', [ConsultoriosController::class, 'update']);
        Route::delete('/deleteConsultorio/{id}', [ConsultoriosController::class, 'delete']);
        Route::get('/consultorioById/{id}', [ConsultoriosController::class, 'consultorioById']);

        // Horarios (gestión)
        Route::post('/addHorario', [HorarioController::class, 'store']);
        Route::get('/horario', [HorarioController::class, 'index']);
        Route::put('/updateHorario/{id}', [HorarioController::class, 'update']);
        Route::delete('/deleteHorario/{id}', [HorarioController::class, 'delete']);
        Route::get('/horarioById/{id}', [HorarioController::class, 'horarioById']);

        // Citas médicas (gestión)
        Route::get('/citas', [CitaController::class, 'index']);
        Route::get('/citaById/{id}', [CitaController::class, 'CitaById']);
        Route::post('/addCita', [CitaController::class, 'store']);
        Route::put('/updateCita/{id}', [CitaController::class, 'update']);
        Route::delete('/deleteCita/{id}', [CitaController::class, 'delete']);
        Route::get('/citaById/{id}', [CitaController::class, 'CitaById']);
    });

    // ===== COMÚN A doctor, paciente y admin =====
    Route::group(['middleware' => ['rol:doctor,paciente,admin']], function () {
        
        Route::get('/consultorios', [ConsultoriosController::class, 'index']);
        Route::get('/doctores', [DoctorController::class, 'index']);
        Route::get('/horarioById/{id}', [HorarioController::class, 'horarioById']);
        Route::post('/addCita', [CitaController::class, 'store']);
        Route::put('/updateCita/{id}', [CitaController::class, 'update']);
        Route::delete('/deleteCita/{id}', [CitaController::class, 'delete']);
        Route::get('/citaById/{id}', [CitaController::class, 'CitaById']);
    });
});
