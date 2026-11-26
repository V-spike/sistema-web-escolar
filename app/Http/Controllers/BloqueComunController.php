<?php

namespace App\Http\Controllers;

use App\Horarios\Hora;
use App\Models\BloqueComun;
use App\Models\Clase;
use App\Models\Jornada;
use App\Models\Materia;
use App\Models\Receso;
use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Bloques comunes: horas que todos los grupos de un grado tienen a la vez (ej. Currículum Ampliado).
 * Ningún grupo de ese grado puede tener otra clase encima.
 */
class BloqueComunController extends Controller
{
    public function index()
    {
        $semestre = Semestre::activo();

        $bloques = $semestre
            ? BloqueComun::with('materia')->where('semestre_id', $semestre->id)
                ->orderBy('grado')->orderBy('dia')->orderBy('hora_inicio')->get()
            : collect();

        return view('bloques-comunes.index', [
            'bloques' => $bloques,
            'materias' => Materia::where('tipo', 'bloque_comun')->where('activo', true)->orderBy('nombre')->get(),
            'semestre' => $semestre,
            'jornada' => Jornada::actual(),
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
            'materia_id' => ['required', Rule::exists('materias', 'id')->where('tipo', 'bloque_comun')],
            'dia' => ['required', 'integer', 'between:1,5'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
        ], [
            'materia_id.exists' => 'Elige una materia de tipo bloque común.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la de inicio.',
        ]);

        $inicio = Hora::aMinutos($datos['hora_inicio']);
        $fin = Hora::aMinutos($datos['hora_fin']);
        $jornada = Jornada::actual();
        $dias = config('escolar.dias');

        if ($inicio < $jornada->entrada || $fin > $jornada->salida) {
            throw ValidationException::withMessages([
                'hora_inicio' => "El bloque debe quedar dentro de la jornada ({$jornada->hora_entrada_corta}–{$jornada->hora_salida_corta}).",
            ]);
        }

        $receso = Receso::all()->first(fn ($r) => $r->seEncimaCon($inicio, $fin));
        if ($receso) {
            throw ValidationException::withMessages(['hora_inicio' => "Se encima con el {$receso->nombre} ({$receso->rango})."]);
        }

        $otro = BloqueComun::with('materia')->where('semestre_id', $semestre->id)->where('grado', $datos['grado'])
            ->where('dia', $datos['dia'])->get()->first(fn ($b) => $b->seEncimaCon($inicio, $fin));
        if ($otro) {
            throw ValidationException::withMessages([
                'hora_inicio' => "{$datos['grado']}.º ya tiene {$otro->materia->nombre} el {$dias[$datos['dia']]} de {$otro->rango}.",
            ]);
        }

        $chocan = Clase::with(['grupo', 'materia'])->where('semestre_id', $semestre->id)->where('dia', $datos['dia'])
            ->whereHas('grupo', fn ($q) => $q->where('grado', $datos['grado']))->get()
            ->filter(fn ($c) => $c->seEncimaCon($inicio, $fin));
        if ($chocan->isNotEmpty()) {
            $detalle = $chocan->map(fn ($c) => "{$c->grupo->nombre} {$c->materia->nombre} ({$c->rango})")->join('; ');
            throw ValidationException::withMessages([
                'hora_inicio' => "Estos grupos ya tienen clase a esa hora: {$detalle}. Muévelas primero.",
            ]);
        }

        BloqueComun::create($datos + ['semestre_id' => $semestre->id]);

        return redirect()->route('bloques-comunes.index')->with('estado', 'Bloque común agregado');
    }

    /**
     * Aquí sí se borra: es configuración del horario, no historial.
     */
    public function destroy(BloqueComun $bloque)
    {
        $bloque->delete();

        return redirect()->route('bloques-comunes.index')->with('estado', 'Bloque común quitado');
    }
}
