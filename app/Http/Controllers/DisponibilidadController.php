<?php

namespace App\Http\Controllers;

use App\Models\Disponibilidad;
use App\Models\Docente;
use App\Models\Franja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisponibilidadController extends Controller
{
    /**
     * Cuadrícula de casillas. Cada casilla vale "dia-franja_id", por ejemplo "2-5".
     */
    public function edit(Docente $docente)
    {
        // Todas, con recesos, porque la cuadrícula los dibuja como fila
        $franjas = Franja::orderBy('orden')->get();

        // Lo que ya tiene guardado, en el mismo formato que el value de las casillas
        $marcadas = Disponibilidad::where('docente_id', $docente->id)
            ->get()
            ->map(fn ($d) => $d->dia . '-' . $d->franja_id)
            ->all();

        return view('docentes.disponibilidad', [
            'docente' => $docente,
            'franjas' => $franjas,
            'marcadas' => $marcadas,
        ]);
    }

    public function update(Request $request, Docente $docente)
    {
        $request->validate([
            'espacios' => ['nullable', 'array'],
            'espacios.*' => ['string', 'regex:/^[1-5]-\d+$/'],
        ], [
            'espacios.*.regex' => 'Uno de los espacios marcados no es válido.',
        ]);

        // Solo se aceptan franjas de clase: un receso nunca se guarda, aunque alguien edite el HTML
        $deClase = Franja::clases()->pluck('id')->all();

        $filas = collect($request->input('espacios', []))
            ->unique()
            ->map(function ($espacio) {
                [$dia, $franja] = explode('-', $espacio);
                return ['dia' => (int) $dia, 'franja_id' => (int) $franja];
            })
            ->filter(fn ($fila) => in_array($fila['franja_id'], $deClase))
            ->values();

        // Borrar las anteriores y crear las nuevas: juntas o ninguna
        DB::transaction(function () use ($docente, $filas) {
            Disponibilidad::where('docente_id', $docente->id)->delete();
            foreach ($filas as $fila) {
                Disponibilidad::create($fila + ['docente_id' => $docente->id]);
            }
        });

        return redirect()
            ->route('docentes.disponibilidad', $docente)
            ->with('estado', "Disponibilidad guardada: {$filas->count()} espacios");
    }
}
