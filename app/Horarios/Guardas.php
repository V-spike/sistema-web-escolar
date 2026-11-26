<?php

namespace App\Horarios;

use App\Models\Aula;
use App\Models\Calificacion;
use App\Models\Clase;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Jornada;
use App\Models\Materia;

/**
 * Cambios en los catálogos que romperían un horario ya acomodado o las calificaciones ya capturadas.
 * Cada método regresa la causa (texto) si el cambio no se puede hacer, o null si sí se puede.
 */
class Guardas
{
    public static function bajaDocente(Docente $docente): ?string
    {
        $n = Clase::where('docente_id', $docente->id)->count();

        return $n ? "No se puede dar de baja: {$docente->nombre_completo} tiene {$n} clases en el horario. Reasígnalas primero." : null;
    }

    public static function cambioDocente(Docente $docente, array $materias, int $horasPlaza): ?string
    {
        $sinPerfil = Clase::with('materia')->where('docente_id', $docente->id)
            ->whereNotIn('materia_id', $materias)->get()->pluck('materia.nombre')->unique();
        if ($sinPerfil->isNotEmpty()) {
            return "No puedes quitar del perfil {$sinPerfil->join(', ')}: {$docente->nombre_completo} ya la da en el horario.";
        }

        $mph = max(1, Jornada::actual()->minutos_por_hora);
        $asignados = Clase::where('docente_id', $docente->id)->get()->sum->minutos;
        if ($asignados > $horasPlaza * $mph) {
            return "La plaza no puede bajar a {$horasPlaza} h: ya tiene " . Hora::duracion($asignados) . ' de clases en el horario.';
        }

        return null;
    }

    public static function bajaGrupo(Grupo $grupo): ?string
    {
        $n = $grupo->clases()->count();
        if ($n) {
            return "No se puede dar de baja: el grupo {$grupo->nombre} tiene {$n} clases en el horario. Quítalas primero.";
        }

        $alumnos = $grupo->alumnos()->where('activo', true)->count();

        return $alumnos ? "No se puede dar de baja: el grupo {$grupo->nombre} tiene {$alumnos} alumnos. Cámbialos de grupo primero." : null;
    }

    public static function cambioGrupo(Grupo $grupo, int $grado, int $semestreId): ?string
    {
        if ($grado === (int) $grupo->grado && $semestreId === (int) $grupo->semestre_id) {
            return null;
        }

        if ($grupo->clases()->exists()) {
            return "No puedes cambiar el grado ni el semestre del grupo {$grupo->nombre}: ya tiene clases en el horario.";
        }

        // Sus alumnos ya tienen calificaciones de las materias de este grado
        if (Calificacion::whereIn('alumno_id', $grupo->alumnos()->pluck('id'))->exists()) {
            return "No puedes cambiar el grado ni el semestre del grupo {$grupo->nombre}: sus alumnos ya tienen calificaciones.";
        }

        return null;
    }

    public static function bajaAula(Aula $aula): ?string
    {
        $n = Clase::where('aula_id', $aula->id)->count();

        return $n ? "No se puede dar de baja: el aula {$aula->clave} tiene {$n} clases en el horario. Cámbialas de aula primero." : null;
    }

    public static function bajaMateria(Materia $materia): ?string
    {
        $n = Clase::where('materia_id', $materia->id)->count();
        if ($n) {
            return "No se puede dar de baja: {$materia->nombre} está {$n} veces en el horario. Quítala primero.";
        }

        // Si se da de baja, sus calificaciones dejarían de salir en las boletas
        return Calificacion::where('materia_id', $materia->id)->exists()
            ? "No se puede dar de baja: {$materia->nombre} ya tiene calificaciones capturadas."
            : null;
    }

    public static function cambioMateria(Materia $materia, int $semestre, string $tipo, int $horas): ?string
    {
        $clases = Clase::where('materia_id', $materia->id)->get();

        if ($clases->isNotEmpty() && ($semestre !== (int) $materia->semestre || $tipo !== $materia->tipo)) {
            return "No puedes cambiar el semestre ni el tipo de {$materia->nombre}: ya está en el horario.";
        }

        // Una materia con números no puede volverse de A o N/A (ni al revés) si ya tiene calificaciones
        if (($semestre !== (int) $materia->semestre || $tipo !== $materia->tipo)
            && Calificacion::where('materia_id', $materia->id)->exists()) {
            return "No puedes cambiar el semestre ni el tipo de {$materia->nombre}: ya tiene calificaciones capturadas.";
        }

        $mph = max(1, Jornada::actual()->minutos_por_hora);
        $maximo = $clases->groupBy('grupo_id')->map->sum('minutos')->max() ?? 0;
        if ($maximo > $horas * $mph) {
            return "{$materia->nombre} no puede bajar a {$horas} h: un grupo ya tiene " . Hora::duracion($maximo) . ' en el horario.';
        }

        return null;
    }
}
