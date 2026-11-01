<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContrasenaController extends Controller
{
    public function mostrarCambiar()
    {
        return view('panel.cambiar-contrasena');
    }

    public function actualizar(Request $request)
    {
        $request->validate([
            'password_actual' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8']
        ]);

        $usuario = $request->user();
        $password = $request->input('password');

        $usuario->password = $password;
        $usuario->save();
        return back()->with('estado', 'Contraseña actualizada');

    }

}
