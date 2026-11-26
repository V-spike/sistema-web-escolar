<?php

namespace App\Http\Controllers;

use App\Calificaciones\Boleta;
use App\Models\Grupo;
use App\Models\Semestre;
use App\Models\User;

/**
 * Boleta interna y provisional de calificaciones, con la estructura del formato oficial.
 * Se exporta con el botón "Imprimir / Guardar PDF" (usa la impresión del navegador).
 */
class BoletaController extends Controller
{
    /** El alumno ve la suya */
    public function mia()
    {
        $alumno = auth()->user();
        $semestre = Semestre::activo();

        return view('boletas.index', [
            'titulo' => 'Mi boleta',
            'boletas' => ($alumno->grupo && $semestre) ? collect([new Boleta($alumno, $semestre)]) : collect(),
            'aviso' => $alumno->grupo ? 'Todavía no hay semestre activo.' : 'Todavía no tienes grupo asignado. Pídeselo a la administración escolar.',
        ]);
    }

    /** El administrador ve la de un alumno */
    public function alumno(User $alumno)
    {
        abort_unless($alumno->rol === 'alumno', 404);
        $semestre = Semestre::activo();

        return view('boletas.index', [
            'titulo' => "Boleta de {$alumno->name}",
            'boletas' => ($alumno->grupo && $semestre) ? collect([new Boleta($alumno, $semestre)]) : collect(),
            'aviso' => 'El alumno no tiene grupo o no hay semestre activo.',
        ]);
    }

    /** Todas las boletas de un grupo: al imprimir sale una por hoja */
    public function grupo(Grupo $grupo)
    {
        $semestre = Semestre::activo();
        abort_unless($semestre && $grupo->semestre_id === $semestre->id, 404);

        $boletas = $grupo->alumnos()->where('activo', true)->orderBy('name')->get()
            ->map(fn ($alumno) => new Boleta($alumno, $semestre));

        return view('boletas.index', [
            'titulo' => "Boletas de {$grupo->nombre}",
            'boletas' => $boletas,
            'aviso' => 'Este grupo no tiene alumnos activos.',
        ]);
    }
}
