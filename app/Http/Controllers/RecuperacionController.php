<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class RecuperacionController extends Controller
{
    public function mostrarFormulario()
    {
        return view('acceso.olvide');
    }

    public function enviarEnlace(Request $request)
    {
        //Primero validar el formato y correo
        $request->validate([
            'email' => ['required', 'email'],
        ]);
        //Pedir que Laravel envíe el enlace al correo
        Password::sendResetLink($request->only('email'));
        // Mandar respuesta exista o no el correo
        return back()->with('estado', 'Si el correo está registrado, te hemos enviado un enlace para cambiar tu contraseña');

    }
}
