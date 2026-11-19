<?php

namespace App\Http\Controllers;

use App\Models\BloqueComun;
use App\Models\Franja;
use App\Models\Materia;
use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BloqueComunController extends Controller
{
    /**
     * Una sola pantalla: arriba el formulario, abajo la lista del semestre activo.
     */
    public function index()
    {
        $semestre = Semestre::activo();

        $bloques = $semestre
            ? BloqueComun::with(['materia', 'franja'])
                ->where('semestre_id', $semestre->id)
                ->orderBy('grado')->orderBy('dia')->orderBy('franja_id')
                ->get()
            : collect();

        return view('bloques-comunes.index', [
            'bloques' => $bloques,
            'materias' => Materia::where('tipo', 'bloque_comun')->where('activo', true)->orderBy('nombre')->get(),
            'franjas' => Franja::clases()->get(),
            'semestre' => $semestre,
        ]);
    }

    public function store(Request $request)
    {
        $semestre = Semestre::activo();

        if (! $semestre) {
            return back()->withErrors(['semestre' => 'Primero marca un semestre como activo.']);
        }

        $datos = $request->validate([
            'grado' => ['required', 'integer', 'between:1,6'],
            'materia_id' => [
                'required',
                Rule::exists('materias', 'id')->where('tipo', 'bloque_comun'),
            ],
            'dia' => ['required', 'integer', 'between:1,5'],
            'franja_id' => [
                'required',
                Rule::exists('franjas', 'id')->where('es_receso', false),
                // El mismo grado no puede tener dos bloques a la misma hora del mismo periodo
                Rule::unique('bloques_comunes')->where(fn ($q) => $q
                    ->where('semestre_id', $semestre->id)
                    ->where('grado', $request->grado)
                    ->where('dia', $request->dia)),
            ],
        ], [
            'materia_id.exists' => 'Elige una materia de tipo bloque común.',
            'franja_id.exists' => 'Elige una franja de clase (no un receso).',
            'franja_id.unique' => 'Ese grado ya tiene un bloque común en ese día y franja.',
        ]);

        BloqueComun::create($datos + ['semestre_id' => $semestre->id]);

        return redirect()->route('bloques-comunes.index')->with('estado', 'Bloque común agregado');
    }

    /**
     * Aquí sí se borra: es configuración del horario, no historial.
     * El parámetro se llama $bloque para coincidir con {bloque} en la ruta.
     */
    public function destroy(BloqueComun $bloque)
    {
        $bloque->delete();

        return redirect()->route('bloques-comunes.index')->with('estado', 'Bloque común quitado');
    }
}
