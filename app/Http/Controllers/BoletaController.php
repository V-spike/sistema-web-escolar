<?php

namespace App\Http\Controllers;

use App\Calificaciones\Boleta;
use App\Models\Clase;
use App\Models\Grupo;
use App\Models\Semestre;
use App\Models\User;

/**
 * Calificaciones del alumno y boletas para imprimir.
 * - El alumno ve sus calificaciones por materia y parcial (una tabla sencilla, no la boleta).
 * - El docente imprime las boletas de los grupos donde da clase; la administración, las de cualquiera.
 * La boleta usa la estructura del formato oficial y se exporta con "Imprimir / Guardar PDF".
 */
class BoletaController extends Controller
{
    /** El alumno: sus materias con la calificación de cada parcial */
    public function misCalificaciones()
    {
        $alumno = auth()->user();
        $semestre = Semestre::activo();

        return view('calificaciones.mias', [
            'boleta' => ($alumno->grupo && $semestre) ? new Boleta($alumno, $semestre) : null,
            'aviso' => $alumno->grupo ? 'Todavía no hay semestre activo.' : 'Todavía no tienes grupo asignado. Pídeselo a la administración escolar.',
        ]);
    }

    /** Boleta de un alumno */
    public function alumno(User $alumno)
    {
        abort_unless($alumno->rol === 'alumno' && $alumno->grupo, 404);
        $semestre = Semestre::activo();
        abort_unless($semestre && $this->puedeVerGrupo($alumno->grupo, $semestre), 403);

        return view('boletas.index', [
            'titulo' => "Boleta de {$alumno->name}",
            'boletas' => collect([new Boleta($alumno, $semestre)]),
            'aviso' => '',
        ]);
    }

    /** Todas las boletas de un grupo: al imprimir sale una por hoja */
    public function grupo(Grupo $grupo)
    {
        $semestre = Semestre::activo();
        abort_unless($semestre && $grupo->semestre_id === $semestre->id, 404);
        abort_unless($this->puedeVerGrupo($grupo, $semestre), 403);

        $boletas = $grupo->alumnos()->where('activo', true)->orderBy('name')->get()
            ->map(fn ($alumno) => new Boleta($alumno, $semestre));

        return view('boletas.index', [
            'titulo' => "Boletas de {$grupo->nombre}",
            'boletas' => $boletas,
            'aviso' => 'Este grupo no tiene alumnos activos.',
        ]);
    }

    /** La administración ve todos los grupos; el docente, solo donde tiene clases este semestre */
    private function puedeVerGrupo(Grupo $grupo, Semestre $semestre): bool
    {
        $usuario = auth()->user();
        if (in_array($usuario->rol, ['administrador', 'superusuario'], true)) {
            return true;
        }

        return $usuario->docente && Clase::where('semestre_id', $semestre->id)
            ->where('docente_id', $usuario->docente->id)->where('grupo_id', $grupo->id)->exists();
    }
}
