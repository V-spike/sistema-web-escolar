<?php

namespace App\Http\Controllers;

use App\Horarios\Hora;
use App\Horarios\Semana;
use App\Models\Clase;
use App\Models\Jornada;
use App\Models\Receso;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * La jornada: hora de entrada, hora de salida, duración de la hora clase y recesos.
 * Ningún cambio se acepta si deja clases ya acomodadas fuera de la jornada o encima de un receso.
 */
class JornadaController extends Controller
{
    public function index()
    {
        return view('jornada.index', [
            'jornada' => Jornada::actual(),
            'recesos' => Receso::orderBy('hora_inicio')->get(),
            'semana' => new Semana(),
        ]);
    }

    public function update(Request $request)
    {
        $datos = $request->validate([
            'hora_entrada' => ['required', 'date_format:H:i'],
            'hora_salida' => ['required', 'date_format:H:i', 'after:hora_entrada'],
            'minutos_por_hora' => ['required', 'integer', 'between:30,60'],
        ], [
            'hora_salida.after' => 'La hora de salida debe ser posterior a la de entrada.',
        ]);

        $entrada = Hora::aMinutos($datos['hora_entrada']);
        $salida = Hora::aMinutos($datos['hora_salida']);

        $fuera = Clase::with(['grupo', 'materia'])->get()
            ->filter(fn ($c) => $c->inicio < $entrada || $c->fin > $salida);
        $this->rechazarSiHay($fuera, 'hora_salida', 'Estas clases quedarían fuera de la nueva jornada');

        $recesoFuera = Receso::all()->first(fn ($r) => $r->inicio < $entrada || $r->fin > $salida);
        if ($recesoFuera) {
            throw ValidationException::withMessages([
                'hora_salida' => "El {$recesoFuera->nombre} ({$recesoFuera->rango}) quedaría fuera de la jornada. Muévelo o quítalo primero.",
            ]);
        }

        $jornada = Jornada::query()->first() ?? new Jornada();
        $jornada->fill($datos)->save();

        return redirect()->route('jornada.index')->with('estado', 'Jornada actualizada');
    }

    public function storeReceso(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:40'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
        ], [
            'hora_fin.after' => 'La hora de fin debe ser posterior a la de inicio.',
        ]);

        $inicio = Hora::aMinutos($datos['hora_inicio']);
        $fin = Hora::aMinutos($datos['hora_fin']);
        $jornada = Jornada::actual();

        if ($inicio < $jornada->entrada || $fin > $jornada->salida) {
            throw ValidationException::withMessages([
                'hora_inicio' => "El receso debe quedar dentro de la jornada ({$jornada->hora_entrada_corta}–{$jornada->hora_salida_corta}).",
            ]);
        }

        $otro = Receso::all()->first(fn ($r) => $r->seEncimaCon($inicio, $fin));
        if ($otro) {
            throw ValidationException::withMessages([
                'hora_inicio' => "Se encima con el {$otro->nombre} ({$otro->rango}).",
            ]);
        }

        $chocan = Clase::with(['grupo', 'materia'])->get()->filter(fn ($c) => $c->seEncimaCon($inicio, $fin));
        $this->rechazarSiHay($chocan, 'hora_inicio', 'El receso se encimaría con estas clases');

        Receso::create($datos);

        return redirect()->route('jornada.index')->with('estado', 'Receso agregado');
    }

    public function destroyReceso(Receso $receso)
    {
        $receso->delete();

        return redirect()->route('jornada.index')->with('estado', 'Receso quitado');
    }

    private function rechazarSiHay($clases, string $campo, string $titulo): void
    {
        if ($clases->isEmpty()) {
            return;
        }

        $dias = config('escolar.dias');
        $detalle = $clases->take(5)->map(fn ($c) => "{$c->grupo->nombre} {$c->materia->nombre} ({$dias[$c->dia]} {$c->rango})")->join('; ');
        $mas = $clases->count() > 5 ? ' y ' . ($clases->count() - 5) . ' más' : '';

        throw ValidationException::withMessages([$campo => "{$titulo}: {$detalle}{$mas}. Muévelas primero."]);
    }
}
