<?php

use App\Http\Controllers\AccesoController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function() {
    //ruta -> /login: lo que el usuario ve. 'mostrarLogin' : metodo, 'login' apodo en el codigo
    Route::get('/login', [AccesoController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AccesoController::class, 'entrar']);

});


Route::middleware('auth')->group(function() {
    Route::post('/logout', [AccesoController::class, 'salir'])->name('logout');
});
