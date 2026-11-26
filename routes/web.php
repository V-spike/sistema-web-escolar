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


//panel de usuarios
Route::middleware(['auth', 'sin-cache'])->prefix('panel')->name('panel.')->group(function () {
    Route::view('/superusuario', 'panel.superusuario')->name('superusuario')->middleware('rol:superusuario');
    Route::view('/administrador', 'panel.administrador')->name('administrador')->middleware('rol:administrador');
    Route::view('/docente', 'panel.docente')->name('docente')->middleware('rol:docente');
    Route::view('/alumno', 'panel.alumno')->name('alumno')->middleware('rol:alumno');
});

// ---------- Sprint 3: solo el superusuario ----------
Route::middleware(['auth', 'sin-cache', 'rol:superusuario'])->prefix('panel')->group(function () {
    // HU-03 · Axel: administradores
});

// ---------- Sprint 3: administrador y superusuario ----------
Route::middleware(['auth', 'sin-cache', 'rol:administrador,superusuario'])->prefix('panel')->group(function () {
    // HU-05 · Marcos: materias
    Route::resource('materias', MateriaController::class)->except(['show', 'destroy']);
    Route::patch('materias/{materia}/estado', [MateriaController::class, 'cambiarEstado'])->name('materias.estado');

    // HU-06 · Marcos: aulas
    Route::resource('aulas', AulaController::class)->except(['show', 'destroy']);
    Route::patch('aulas/{aula}/estado', [AulaController::class, 'cambiarEstado'])->name('aulas.estado');

    // HU-07 · Axel: semestres
    Route::resource('semestres', SemestreController::class)->except(['show', 'destroy']);
    Route::patch('semestres/{semestre}/activar', [SemestreController::class, 'activar'])->name('semestres.activar');

    // HU-25 · Axel: bloques comunes
    Route::get('bloques-comunes', [BloqueComunController::class, 'index'])->name('bloques-comunes.index');
    Route::post('bloques-comunes', [BloqueComunController::class, 'store'])->name('bloques-comunes.store');
    Route::delete('bloques-comunes/{bloque}', [BloqueComunController::class, 'destroy'])->name('bloques-comunes.destroy');

    // HU-12 · Axel: jornada (solo se editan las horas)
    Route::get('jornada', [JornadaController::class, 'index'])->name('jornada.index');
    Route::get('jornada/{franja}/editar', [JornadaController::class, 'edit'])->name('jornada.edit');
    Route::put('jornada/{franja}', [JornadaController::class, 'update'])->name('jornada.update');

    // HU-13 · Axel: disponibilidad del docente
    Route::get('docentes/{docente}/disponibilidad', [DisponibilidadController::class, 'edit'])->name('docentes.disponibilidad');
    Route::put('docentes/{docente}/disponibilidad', [DisponibilidadController::class, 'update'])->name('docentes.disponibilidad.guardar');
});

// HU-10 · Marcos
Route::resource('docentes', DocenteController::class)->except(['show', 'destroy']);
Route::patch('docentes/{docente}/estado', [DocenteController::class, 'cambiarEstado'])->name('docentes.estado');

// HU-08 · Marcos
Route::resource('grupos', GrupoController::class)->except(['show', 'destroy']);
Route::patch('grupos/{grupo}/estado', [GrupoController::class, 'cambiarEstado'])->name('grupos.estado');