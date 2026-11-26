<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * HU-03: el superusuario da de alta, edita y da de baja administradores.
 */
class AdministradorController extends Controller
{
    public function index()
    {
        $administradores = User::where('rol', 'administrador')->orderByDesc('activo')->orderBy('name')->get();

        return view('administradores.index', compact('administradores'));
    }

    public function create()
    {
        return view('administradores.crear');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create($datos + ['rol' => 'administrador', 'activo' => true]);

        return redirect()->route('administradores.index')->with('estado', 'Administrador registrado');
    }

    public function edit(User $administrador)
    {
        abort_unless($administrador->rol === 'administrador', 404);

        return view('administradores.editar', compact('administrador'));
    }

    public function update(Request $request, User $administrador)
    {
        abort_unless($administrador->rol === 'administrador', 404);

        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($administrador->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        $administrador->update($datos);

        return redirect()->route('administradores.index')->with('estado', 'Administrador actualizado');
    }

    public function cambiarEstado(User $administrador)
    {
        abort_unless($administrador->rol === 'administrador', 404);

        $administrador->update(['activo' => ! $administrador->activo]);

        return redirect()->route('administradores.index')
            ->with('estado', $administrador->activo ? 'Administrador reactivado' : 'Administrador dado de baja');
    }
}
