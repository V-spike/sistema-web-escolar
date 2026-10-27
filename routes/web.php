<?php

use App\Http\Controllers\AccesoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::middleware('guest')->group(function () {
    //ruta -> /login: lo que el usuario ve. 'mostrarLogin' : metodo, 'login' apodo en el codigo
    Route::get('/login', [AccesoController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AccesoController::class, 'entrar']);
});


Route::middleware('auth')->group(function () {
    Route::post('/logout', [AccesoController::class, 'salir'])->name('logout');
});


//panel de usuarioos
Route::middleware('auth')->prefix('panel')->name('panel.')->group(function () {
    Route::view('/superusuario', 'panel.superusuario')->name('superusuario');
    Route::view('/administrador','panel.administrador')->name('administrador');
    Route::view('/docente','panel.docetnte' )->name('docente');
    Route::view('/alumno', 'panel.alumno')->name('alumno');
});

