<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Horarios\Guardas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MateriaController extends Controller
{
    public function index()
    {
        $materias = Materia::orderByDesc('activo')->orderBy('nombre')->get();
        return view('materias.index', ['materias' => $materias]);
    }

    public function create()
    {
        return view('materias.crear');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'clave' => ['required', 'string', 'max:20', 'unique:materias,clave'],
            'nombre' => ['required', 'string', 'max:100'],
            'horas_semana' => ['required', 'integer', 'between:1,48'],
            'clave_corta' => ['required', 'string', 'max:10'],
            'semestre' => ['required', 'integer', 'between:1,6'],
            'tipo' => ['required', Rule::in(['normal', 'bloque_comun', 'tutoria'])],
        ]);

        Materia::create($datos);

        return redirect()->route('materias.index')->with('estado', 'Materia registrada');
    }

    public function edit(Materia $materia)
    {
        return view('materias.editar', ['materia' => $materia]);
    }

    public function update(Request $request, Materia $materia)
    {
        $datos = $request->validate([
            'clave' => ['required', 'string', 'max:20', Rule::unique('materias', 'clave')->ignore($materia->id)],
            'nombre' => ['required', 'string', 'max:100'],
            'horas_semana' => ['required', 'integer', 'between:1,48'],
            'clave_corta' => ['required', 'string', 'max:10'],
            'semestre' => ['required', 'integer', 'between:1,6'],
            'tipo' => ['required', Rule::in(['normal', 'bloque_comun', 'tutoria'])],
        ]);

        if ($causa = Guardas::cambioMateria($materia, (int) $datos['semestre'], $datos['tipo'], (int) $datos['horas_semana'])) {
            return back()->withInput()->withErrors(['horas_semana' => $causa]);
        }

        $materia->update($datos);

        return redirect()->route('materias.index')->with('estado', 'Materia actualizada');
    }

    public function cambiarEstado(Materia $materia)
    {
        if ($materia->activo && $causa = Guardas::bajaMateria($materia)) {
            return redirect()->route('materias.index')->withErrors(['estado' => $causa]);
        }

        $materia->activo = !$materia->activo;
        $materia->save();

        $mensaje = $materia->activo ? 'Materia reactivada' : 'Materia dada de baja';

        return redirect()->route('materias.index')->with('estado', $mensaje);
    }
}