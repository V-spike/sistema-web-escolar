<?php

namespace App\Http\Controllers;

use App\Models\Franja;
use Illuminate\Http\Request;

class JornadaController extends Controller
{
    /**
     * La jornada no se crea ni se da de baja desde la pantalla: solo se ajustan horas.
     * Por eso no hay create, store ni estado.
     */
    public function index()
    {
        $franjas = Franja::orderBy('orden')->get();
        return view('jornada.index', ['franjas' => $franjas]);
    }

    public function edit(Franja $franja)
    {
        return view('jornada.editar', ['franja' => $franja]);
    }

    public function update(Request $request, Franja $franja)
    {
        $datos = $request->validate([
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
        ], [
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        // Una casilla sin marcar no se envía; boolean() la convierte en false
        $datos['es_receso'] = $request->boolean('es_receso');

        $franja->update($datos);

        return redirect()->route('jornada.index')->with('estado', 'Franja actualizada');
    }
}
