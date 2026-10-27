<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccesoController extends Controller
{
    // métodos
    public function mostrarLogin()
    {
        return view('acceso.login');
    }



    public function salir(Request $request)
    {
        Auth::logout(); // olvida quien es el usuario
        $request->session()->invalidate();//lo manda a la basura
        $request->session()->regenerateToken();// crea un nuevo token
        return redirect()->route('login');//redirecciona a login
    }

}
