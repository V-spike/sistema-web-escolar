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
        $request->session()->invalidate(); //lo manda a la basura
        $request->session()->regenerateToken(); // crea un nuevo token
        return redirect()->route('login'); //redirecciona a login
    }

    public function entrar(Request $request)
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credenciales['activo'] = true;

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();//cambia el id de la sesion
            return redirect()->route('panel.' . Auth::user()->rol);
        }
        return back()->withErrors(['email' => 'Correo o contraseña incorrectos'])->onlyInput('email');
    }
}
