<?php

use App\Http\Controllers\AccesoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::middleware('guest')->group(function() {
    //ruta -> /login: lo que el usuario ve. 'mostrarLogin' : metodo, 'login' apodo en el codigo
    Route::get('/login', [AccesoController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AccesoController::class, 'entrar']);

});


Route::middleware('auth')->group(function() {
Route::post('/logout', [AccesoController::class, 'salir'])->name('logout');
});

// métodos

class mostrarLogin extends AccesoController 
{
    public function login()
    {
        return view('acceso.login');
    }
}

class salir extends AccesoController
{
    public function logout(Request $request)
    {
        Auth::logout(); // olvida quien es el usuario
        $request->session()->invalidate();//lo manda a la basura
        $request->session()->regenerateToken();// crea un nuevo token
        return redirect()->route('login');//redirecciona a login
    }
}
