<?php

namespace App\Http\Controllers;

use App\Horarios\RevisorEmpalmes;
use App\Horarios\Semana;
use App\Models\Clase;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Jornada;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\User;

/**
 * Página de inicio de cada rol. Docentes y alumnos ven su horario de la semana.
 */
class PanelController extends Controller
{
    public function administrador(RevisorEmpalmes $revisor)
    {
        return view('panel.administrador', $this->resumen($revisor));
    }

    public function superusuario(RevisorEmpalmes $revisor)
    {
        return view('panel.superusuario', $this->resumen($revisor) + [
            'administradores' => User::where('rol', 'administrador')->where('activo', true)->count(),
        ]);
    }

    public function docente()
    {
        $docente = auth()->user()->docente;
        $semestre = Semestre::activo();
        $semana = new Semana();
        $clases = collect();

        if ($docente && $semestre) {
            $clases = Clase::with(['grupo', 'materia', 'docente', 'aula'])
                ->where('semestre_id', $semestre->id)->where('docente_id', $docente->id)
                ->orderBy('dia')->orderBy('hora_inicio')->get();
            $semana->conClases($clases, 'docente');
        }

        return view('panel.docente', compact('docente', 'semestre', 'semana', 'clases'));
    }

    public function alumno()
    {
        $grupo = auth()->user()->grupo;
        $semestre = Semestre::activo();
        $semana = new Semana();
        $clases = collect();

        if ($grupo && $semestre) {
            $clases = Clase::with(['grupo', 'materia', 'docente', 'aula'])
                ->where('semestre_id', $semestre->id)->where('grupo_id', $grupo->id)
                ->orderBy('dia')->orderBy('hora_inicio')->get();
            $semana->conClases($clases, 'alumno');
        }

        return view('panel.alumno', compact('grupo', 'semestre', 'semana', 'clases'));
    }

    private function resumen(RevisorEmpalmes $revisor): array
    {
        $semestre = Semestre::activo();
        $mph = max(1, Jornada::actual()->minutos_por_hora);
        $grupos = $semestre ? Grupo::where('semestre_id', $semestre->id)->where('activo', true)->orderBy('grado')->orderBy('letra')->get() : collect();

        // Avance de cada grupo: horas acomodadas contra horas que pide su grado
        $avance = $grupos->map(function ($grupo) use ($mph) {
            $requeridas = Materia::where('activo', true)->where('semestre', $grupo->grado)
                ->sum('horas_semana');
            $asignadas = round($grupo->clases()->get()->sum->minutos / $mph, 1);

            return [
                'grupo' => $grupo,
                'asignadas' => $asignadas,
                'requeridas' => (int) $requeridas,
                'porcentaje' => $requeridas ? min(100, (int) round($asignadas * 100 / $requeridas)) : 0,
            ];
        });

        return [
            'semestre' => $semestre,
            'avance' => $avance,
            'empalmes' => $semestre ? count($revisor->revisarSemestre($semestre)) : 0,
            'totales' => [
                'grupos' => $grupos->count(),
                'docentes' => Docente::where('activo', true)->count(),
                'clases' => $semestre ? Clase::where('semestre_id', $semestre->id)->count() : 0,
                'alumnos' => User::where('rol', 'alumno')->where('activo', true)->count(),
            ],
        ];
    }
}
