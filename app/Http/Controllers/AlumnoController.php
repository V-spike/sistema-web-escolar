<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Alumnos: cuentas con rol alumno ligadas a un grupo, para que vean su horario.
 */
class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $grupoId = $request->integer('grupo') ?: null;

        $alumnos = User::with('grupo')->where('rol', 'alumno')
            ->when($grupoId, fn ($q) => $q->where('grupo_id', $grupoId))
            ->orderByDesc('activo')->orderBy('name')->get();

        return view('alumnos.index', [
            'alumnos' => $alumnos,
            'grupos' => $this->grupos(),
            'grupoId' => $grupoId,
        ]);
    }

    public function create()
    {
        return view('alumnos.crear', ['grupos' => $this->grupos()]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas() + [
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create($datos + ['rol' => 'alumno', 'activo' => true]);

        return redirect()->route('alumnos.index')->with('estado', 'Alumno registrado');
    }

    public function edit(User $alumno)
    {
        abort_unless($alumno->rol === 'alumno', 404);

        return view('alumnos.editar', ['alumno' => $alumno, 'grupos' => $this->grupos()]);
    }

    public function update(Request $request, User $alumno)
    {
        abort_unless($alumno->rol === 'alumno', 404);

        $datos = $request->validate($this->reglas($alumno) + [
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        $alumno->update($datos);

        return redirect()->route('alumnos.index')->with('estado', 'Alumno actualizado');
    }

    public function cambiarEstado(User $alumno)
    {
        abort_unless($alumno->rol === 'alumno', 404);

        $alumno->update(['activo' => ! $alumno->activo]);

        return redirect()->route('alumnos.index')
            ->with('estado', $alumno->activo ? 'Alumno reactivado' : 'Alumno dado de baja');
    }

    private function reglas(?User $alumno = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'matricula' => ['required', 'string', 'max:20', Rule::unique('users', 'matricula')->ignore($alumno?->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($alumno?->id)],
            'grupo_id' => ['required', Rule::exists('grupos', 'id')->where('activo', true)],
        ];
    }

    private function grupos()
    {
        $semestre = Semestre::activo();

        return Grupo::when($semestre, fn ($q) => $q->where('semestre_id', $semestre->id))
            ->where('activo', true)->orderBy('grado')->orderBy('letra')->get();
    }
}
