<?php

use App\Http\Controllers\AccesoController;
use App\Http\Controllers\RecuperacionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContrasenaController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\SemestreController;
use App\Http\Controllers\JornadaController;
use App\Http\Controllers\BloqueComunController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AdministradorController;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    //ruta -> /login: lo que el usuario ve. 'mostrarLogin' : metodo, 'login' apodo en el codigo
    Route::get('/login', [AccesoController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AccesoController::class, 'entrar']);
    Route::get('/olvide-contrasena', [RecuperacionController::class, 'mostrarFormulario'])->name('password.request');
    Route::post('/olvide-contrasena', [RecuperacionController::class, 'enviarEnlace'])->name('password.email');
    Route::get('/restablecer/{token}', [RecuperacionController::class, 'mostrarRestablecer'])->name('password.reset');
    Route::post('/restablecer', [RecuperacionController::class, 'restablecer'])->name('password.update');
});


Route::middleware(['auth', 'sin-cache'])->group(function () {
    Route::post('/logout', [AccesoController::class, 'salir'])->name('logout');
    Route::get('/cambiar-contrasena', [ContrasenaController::class, 'mostrarCambiar'])->name('contrasena.editar');
    Route::post('/cambiar-contrasena', [ContrasenaController::class, 'actualizar'])->name('contrasena.actualizar');

});


// Inicio de cada rol
Route::middleware(['auth', 'sin-cache'])->prefix('panel')->name('panel.')->group(function () {
    Route::get('/superusuario', [PanelController::class, 'superusuario'])->name('superusuario')->middleware('rol:superusuario');
    Route::get('/administrador', [PanelController::class, 'administrador'])->name('administrador')->middleware('rol:administrador');
    Route::get('/docente', [PanelController::class, 'docente'])->name('docente')->middleware('rol:docente');
    Route::get('/alumno', [PanelController::class, 'alumno'])->name('alumno')->middleware('rol:alumno');
});

// ---------- Solo el superusuario ----------
Route::middleware(['auth', 'sin-cache', 'rol:superusuario'])->prefix('panel')->group(function () {
    // HU-03: administradores
    Route::resource('administradores', AdministradorController::class)
        ->parameters(['administradores' => 'administrador'])->except(['show', 'destroy']);
    Route::patch('administradores/{administrador}/estado', [AdministradorController::class, 'cambiarEstado'])->name('administradores.estado');
});

// ---------- Administrador y superusuario ----------
Route::middleware(['auth', 'sin-cache', 'rol:administrador,superusuario'])->prefix('panel')->group(function () {
    // Horario: acomodar clases sin empalmes
    Route::get('horarios', [HorarioController::class, 'index'])->name('horarios.index');
    Route::post('horarios/revisar', [HorarioController::class, 'revisar'])->name('horarios.revisar');
    Route::post('horarios', [HorarioController::class, 'store'])->name('horarios.store');
    Route::get('horarios/{clase}/editar', [HorarioController::class, 'edit'])->name('horarios.edit');
    Route::put('horarios/{clase}', [HorarioController::class, 'update'])->name('horarios.update');
    Route::delete('horarios/{clase}', [HorarioController::class, 'destroy'])->name('horarios.destroy');

    // HU-05: materias
    Route::resource('materias', MateriaController::class)->except(['show', 'destroy']);
    Route::patch('materias/{materia}/estado', [MateriaController::class, 'cambiarEstado'])->name('materias.estado');

    // HU-06: aulas
    Route::resource('aulas', AulaController::class)->except(['show', 'destroy']);
    Route::patch('aulas/{aula}/estado', [AulaController::class, 'cambiarEstado'])->name('aulas.estado');

    // HU-07: semestres
    Route::resource('semestres', SemestreController::class)->except(['show', 'destroy']);
    Route::patch('semestres/{semestre}/activar', [SemestreController::class, 'activar'])->name('semestres.activar');

    // HU-12: jornada y recesos
    Route::get('jornada', [JornadaController::class, 'index'])->name('jornada.index');
    Route::put('jornada', [JornadaController::class, 'update'])->name('jornada.update');
    Route::post('jornada/recesos', [JornadaController::class, 'storeReceso'])->name('jornada.recesos.store');
    Route::delete('jornada/recesos/{receso}', [JornadaController::class, 'destroyReceso'])->name('jornada.recesos.destroy');

    // HU-25: bloques comunes
    Route::get('bloques-comunes', [BloqueComunController::class, 'index'])->name('bloques-comunes.index');
    Route::post('bloques-comunes', [BloqueComunController::class, 'store'])->name('bloques-comunes.store');
    Route::delete('bloques-comunes/{bloque}', [BloqueComunController::class, 'destroy'])->name('bloques-comunes.destroy');

    // HU-10: docentes
    Route::resource('docentes', DocenteController::class)->except(['show', 'destroy']);
    Route::patch('docentes/{docente}/estado', [DocenteController::class, 'cambiarEstado'])->name('docentes.estado');

    // HU-13: disponibilidad del docente (rangos de hora)
    Route::get('docentes/{docente}/disponibilidad', [DisponibilidadController::class, 'edit'])->name('docentes.disponibilidad');
    Route::post('docentes/{docente}/disponibilidad', [DisponibilidadController::class, 'store'])->name('docentes.disponibilidad.guardar');
    Route::post('docentes/{docente}/disponibilidad/completa', [DisponibilidadController::class, 'completa'])->name('docentes.disponibilidad.completa');
    Route::delete('docentes/{docente}/disponibilidad/{rango}', [DisponibilidadController::class, 'destroy'])->name('docentes.disponibilidad.quitar');

    // HU-08: grupos
    Route::resource('grupos', GrupoController::class)->except(['show', 'destroy']);
    Route::patch('grupos/{grupo}/estado', [GrupoController::class, 'cambiarEstado'])->name('grupos.estado');

    // Alumnos
    Route::resource('alumnos', AlumnoController::class)->except(['show', 'destroy']);
    Route::patch('alumnos/{alumno}/estado', [AlumnoController::class, 'cambiarEstado'])->name('alumnos.estado');
});
