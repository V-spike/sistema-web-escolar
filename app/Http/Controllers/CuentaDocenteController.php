<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * La cuenta con la que un docente entra al sistema para ver "Mi horario".
 * Si todavía no tiene, se crea; si ya tiene, se cambia el correo o la contraseña.
 */
class CuentaDocenteController extends Controller
{
    public function guardar(Request $request, Docente $docente)
    {
        $cuenta = $docente->usuario;

        $datos = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($cuenta?->id)],
            // Al crear la cuenta la contraseña es obligatoria; al editarla, solo si se quiere cambiar
            'password' => [$cuenta ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ], [], ['email' => 'correo de acceso']);

        if ($cuenta) {
            $cuenta->email = $datos['email'];
            if (! empty($datos['password'])) {
                $cuenta->password = $datos['password'];
            }
            $cuenta->save();

            return redirect()->route('docentes.edit', $docente)->with('estado', 'Cuenta del docente actualizada');
        }

        $cuenta = User::create([
            'name' => $docente->nombre_completo,
            'email' => $datos['email'],
            'password' => $datos['password'],
            'rol' => 'docente',
            'activo' => $docente->activo,
        ]);
        $docente->update(['user_id' => $cuenta->id]);

        return redirect()->route('docentes.edit', $docente)->with('estado', 'Cuenta creada: el docente ya puede entrar con ese correo');
    }
}
