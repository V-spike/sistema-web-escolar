<?php

namespace App\Http\Controllers;

use App\Horarios\Hora;
use App\Horarios\Semana;
use App\Models\Clase;
use App\Models\Disponibilidad;
use App\Models\Docente;
use App\Models\Jornada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Disponibilidad del docente como rangos de hora por día (ej. lunes 15:00–19:30).
 * No se acepta un cambio que deje fuera clases que ya tiene en el horario.
 */
class DisponibilidadController extends Controller
{
    public function edit(Docente $docente)
    {
        $rangos = $docente->disponibilidades()->orderBy('dia')->orderBy('hora_inicio')->get();
        $clases = Clase::with(['grupo', 'materia', 'docente', 'aula'])->where('docente_id', $docente->id)->get();

        $semana = (new Semana())->conRangos($rangos, 'Disponible')->conClases($clases, 'docente');

        return view('docentes.disponibilidad', [
            'docente' => $docente,
            'rangos' => $rangos,
            'semana' => $semana,
            'jornada' => Jornada::actual(),
        ]);
    }

    public function store(Request $request, Docente $docente)
    {
        $datos = $request->validate([
            'dias' => ['required', 'array', 'min:1'],
            'dias.*' => ['integer', 'between:1,5'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
        ], [
            'dias.required' => 'Marca al menos un día.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la de inicio.',
        ]);

        $inicio = Hora::aMinutos($datos['hora_inicio']);
        $fin = Hora::aMinutos($datos['hora_fin']);
        $jornada = Jornada::actual();

        if ($inicio < $jornada->entrada || $fin > $jornada->salida) {
            throw ValidationException::withMessages([
                'hora_inicio' => "El rango debe quedar dentro de la jornada ({$jornada->hora_entrada_corta}–{$jornada->hora_salida_corta}).",
            ]);
        }

        // Si se encima o toca otro rango del mismo día, se unen en uno solo
        DB::transaction(function () use ($docente, $datos, $inicio, $fin) {
            foreach (array_unique($datos['dias']) as $dia) {
                $nuevoInicio = $inicio;
                $nuevoFin = $fin;
                $juntar = $docente->disponibilidades()->where('dia', $dia)->get()
                    ->filter(fn ($r) => $r->inicio <= $fin && $r->fin >= $inicio);

                foreach ($juntar as $r) {
                    $nuevoInicio = min($nuevoInicio, $r->inicio);
                    $nuevoFin = max($nuevoFin, $r->fin);
                    $r->delete();
                }

                $docente->disponibilidades()->create([
                    'dia' => $dia,
                    'hora_inicio' => Hora::aTexto($nuevoInicio),
                    'hora_fin' => Hora::aTexto($nuevoFin),
                ]);
            }
        });

        return redirect()->route('docentes.disponibilidad', $docente)->with('estado', 'Disponibilidad guardada');
    }

    /** "Disponible toda la jornada" de lunes a viernes */
    public function completa(Docente $docente)
    {
        $jornada = Jornada::actual();

        DB::transaction(function () use ($docente, $jornada) {
            $docente->disponibilidades()->delete();
            foreach (array_keys(config('escolar.dias')) as $dia) {
                $docente->disponibilidades()->create([
                    'dia' => $dia,
                    'hora_inicio' => $jornada->hora_entrada_corta,
                    'hora_fin' => $jornada->hora_salida_corta,
                ]);
            }
        });

        return redirect()->route('docentes.disponibilidad', $docente)->with('estado', 'Disponible toda la jornada, de lunes a viernes');
    }

    public function destroy(Docente $docente, Disponibilidad $rango)
    {
        abort_unless((int) $rango->docente_id === (int) $docente->id, 404);

        // Las clases de ese día deben seguir cabiendo en lo que quede
        $quedan = $docente->disponibilidades()->where('dia', $rango->dia)->whereKeyNot($rango->id)->get();
        $sinLugar = Clase::with(['grupo', 'materia'])->where('docente_id', $docente->id)->where('dia', $rango->dia)->get()
            ->filter(fn ($c) => ! $quedan->contains(fn ($q) => $q->inicio <= $c->inicio && $q->fin >= $c->fin));

        if ($sinLugar->isNotEmpty()) {
            $detalle = $sinLugar->map(fn ($c) => "{$c->grupo->nombre} {$c->materia->nombre} ({$c->rango})")->join('; ');

            return back()->withErrors([
                'rango' => "No se puede quitar: {$docente->nombre_completo} tiene clases en ese horario: {$detalle}. Muévelas primero.",
            ]);
        }

        $rango->delete();

        return redirect()->route('docentes.disponibilidad', $docente)->with('estado', 'Rango quitado');
    }
}
