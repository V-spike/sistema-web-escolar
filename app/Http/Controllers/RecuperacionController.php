<?php

namespace App\Http\Controllers;
use App\Models\User;
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

    public function mostrarRestablecer(Request $request, string $token)
    {
        return view('acceso.restablecer', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function restablecer(Request $request)
    {
        // 1) Validar. 'confirmed' exige que password_confirmation sea igual a password
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // 2) Revisar el token y, si es válido, ejecutar la función
        $estado = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $usuario, string $password) {
                $usuario->password = $password;   // User.php la convierte a hash solo
                $usuario->save();                 // guarda en la BD
            }
        );

        // 3) Si salió bien, al login con aviso; si no, de regreso con error
        if ($estado === Password::PasswordReset) {
            return redirect()->route('login')->with('estado', 'Tu contraseña se cambió. Ya puedes iniciar sesión.');
        }
        return back()->withErrors(['email' => 'El enlace no es válido o ya caducó. Solicita uno nuevo.']);
    }
}
