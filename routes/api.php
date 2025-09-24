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

//Rutas protegidas con autenticación JWT
Route::middleware(['jwt.auth'])->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/mi-perfil', [UserController::class, 'miPerfil']);
    Route::put('/actualizar-perfil', [UserController::class, 'actualizarPerfil']);

    //Admin
    Route::middleware(['rol:admin'])->group(function () {

        //Roles
        Route::post('/addRol',[RoleControler::class,'store']);
        Route::get('/roles',[RoleControler::class,'index']);
        Route::put('/updateRoles/{id}',[RoleControler::class,'update']);
        Route::delete('/deleteRol/{id}',[RoleControler::class,'delete']);
        Route::get('/rolById/{id}',[RoleControler::class,'rolById']);

        //User
        Route::post('/addUser',[UserController::class,'store']);
        Route::get('/users',[UserController::class,'index']);
        Route::put('/updateUser/{id}',[UserController::class,'update']);
        Route::delete('/deleteUser/{id}',[UserController::class,'delete']);
        Route::get('/userById/{id}',[UserController::class,'userById']);
        Route::put('/actualizar-perfil-admin/{id}',[UserController::class,'actualizarPerfilAdmin']);

        //Doctores
        Route::post('/addDoctor',[DoctorController::class,'store']);
        Route::get('/doctores',[DoctorController::class,'index']);
        Route::put('/updateDoctor/{id}',[DoctorController::class,'update']);
        Route::delete('/deleteDoctor/{id}',[DoctorController::class,'delete']);
        Route::get('/DoctorById/{id}',[DoctorController::class,'DoctorById']);

        //Especialidades
        Route::post('/addEspecialidad',[EspecialidadController::class,'store']);
        Route::get('/Especialidades',[EspecialidadController::class,'index']);
        Route::put('/updateEspecialidad/{id}',[EspecialidadController::class,'update']);
        Route::delete('/deleteEspecialidad/{id}',[EspecialidadController::class,'delete']);
        Route::get('/especialidadById/{id}',[EspecialidadController::class,'especialidadById']);

        //Pacientes
        Route::post('/addPaciete',[PacienteController::class,'store']);
        Route::get('/pacientes',[PacienteController::class,'index']);
        Route::put('/updatePaciente/{id}',[PacienteController::class,'update']);
        Route::delete('/deletePaciente/{id}',[PacienteController::class,'delete']);
        Route::get('/pacienteById/{id}',[PacienteController::class,'pacienteById']);

        //Consultorios
        Route::post('/addConsultorio',[ConsultoriosController::class,'store']);
        Route::get('/consultorios',[ConsultoriosController::class,'index']);
        Route::put('/updateConsultorio/{id}',[ConsultoriosController::class,'update']);
        Route::delete('/deleteConsultorio/{id}',[ConsultoriosController::class,'delete']);
        Route::get('/consultorioById/{id}',[ConsultoriosController::class,'consultorioById']);

        //Horarios
        Route::post('/addHorario',[HorarioController::class,'store']);
        Route::get('/horario',[HorarioController::class,'index']);
        Route::put('/updateHorario/{id}',[HorarioController::class,'update']);
        Route::delete('/deleteHorario/{id}',[HorarioController::class,'delete']);
        Route::get('/horarioById/{id}',[HorarioController::class,'horarioById']);

        //Citas médicas
        Route::get('/citas',[CitaController::class,'index']);
        Route::get('/citaById/{id}',[CitaController::class,'CitaById']);
    });

    // Doctores
    Route::middleware(['rol:doctor'])->group(function () {

        //Perfil del doctor
        Route::get('/mi-perfil', [DoctorController::class, 'miPerfil']);
        Route::put('/actualizar-perfil', [DoctorController::class, 'actualizarPerfil']);

        //Citas del doctor
        Route::get('/mis-citas', [DoctorController::class, 'misCitas']);
        Route::get('/mis-citas-pendientes', [DoctorController::class, 'misCitasPendientes']);
        Route::put('/aprobar-cita/{id}', [DoctorController::class, 'aprobarCita']);
        Route::put('/rechazar-cita/{id}', [DoctorController::class, 'rechazarCita']);

        //Horarios del doctor
        Route::get('/mis-horarios', [HorarioController::class, 'misHorarios']);
        Route::post('/addHorario',[HorarioController::class,'store']);
        Route::put('/updateHorario/{id}',[HorarioController::class,'update']);
        Route::delete('/deleteHorario/{id}',[HorarioController::class,'delete']);
        Route::get('/horarioById/{id}',[HorarioController::class,'horarioById']);

        //Consultorio del doctor
        Route::get('/mi-consultorio', [ConsultoriosController::class, 'miConsultorio']);
    });

    // Pacientes
    Route::middleware(['rol:paciente'])->group(function () {

        //Perfil del paciente
        Route::get('/mi-perfil', [PacienteController::class, 'miPerfil']);
        Route::put('/actualizar-perfil', [PacienteController::class, 'actualizarPerfil']);

        //Citas del paciente
        Route::get('/mis-citas', [PacienteController::class, 'misCitas']);
        Route::post('/solicitar-cita', [PacienteController::class, 'solicitarCita']);
        Route::get('/doctores-disponibles', [PacienteController::class, 'doctoresDisponibles']);
        Route::get('/horarios-disponibles/{doctor_id}', [PacienteController::class, 'horariosDisponibles']);
    });

    // Doctor y Paciente
    
    Route::group(['middleware' => ['rol:doctor,paciente']], function () {
        Route::post('/addCita',[CitaController::class,'store']);
        Route::put('/updateCita/{id}',[CitaController::class,'update']);
        Route::delete('/deleteCita/{id}',[CitaController::class,'delete']);
        Route::get('/citaById/{id}',[CitaController::class,'CitaById']);
    });
});
