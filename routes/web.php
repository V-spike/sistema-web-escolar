<?php

use App\Http\Controllers\AccesoController;
use App\Http\Controllers\RecuperacionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContrasenaController;

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
    Route::view('/superusuario', 'panel.superusuario')->name('superusuario');
    Route::view('/administrador', 'panel.administrador')->name('administrador');
    Route::view('/docente', 'panel.docente')->name('docente');
    Route::view('/alumno', 'panel.alumno')->name('alumno');
});

