<?php

namespace App\Horarios;

use App\Models\Aula;
use App\Models\Clase;
use App\Models\Disponibilidad;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Jornada;
use App\Models\Materia;
use App\Models\Receso;
use App\Models\Semestre;

/**
 * Revisa si una clase se puede poner en el horario sin empalmarse con nada.
 *
 * Empalmes (no se guarda):
 *  - horas inválidas, fuera de la jornada o encima de un receso
 *  - el grupo, el docente o el aula ya tienen otra clase a esa hora
 *  - el docente no imparte esa materia o no está disponible a esa hora
 *  - la materia no es del grado del grupo, o algo está dado de baja
 *  - el grupo ya completó las horas de la materia o el docente las de su plaza
 *
 * Avisos (sí se guarda): sin aula, aula más chica que el grupo, más de 2 horas de la misma
 * materia el mismo día, docente sin disponibilidad capturada.
 */
class RevisorEmpalmes
{
    private array $dias;

    public function __construct()
    {
        $this->dias = config('escolar.dias');
    }

    /**
     * @param  array  $datos  semestre_id, grupo_id, materia_id, docente_id, aula_id, dia, hora_inicio, hora_fin
     * @param  int|null  $ignorarId  la clase que se está editando (no choca consigo misma)
     */
    public function revisar(array $datos, ?int $ignorarId = null): Revision
    {
        $r = new Revision();

        $semestre = Semestre::find($datos['semestre_id'] ?? null);
        $grupo = Grupo::find($datos['grupo_id'] ?? null);
        $materia = Materia::find($datos['materia_id'] ?? null);
        $docente = Docente::find($datos['docente_id'] ?? null);
        $aula = empty($datos['aula_id']) ? null : Aula::find($datos['aula_id']);
        $dia = (int) ($datos['dia'] ?? 0);
        $inicio = Hora::aMinutos($datos['hora_inicio'] ?? null);
        $fin = Hora::aMinutos($datos['hora_fin'] ?? null);

        // 1. Datos básicos: sin esto no hay nada que comparar
        if (! $semestre) $r->empalme('semestre', 'No hay un semestre activo. Activa uno en Semestres.');
        if (! $grupo) $r->empalme('grupo', 'Elige el grupo.');
        if (! $materia) $r->empalme('materia', 'Elige la materia.');
        if (! $docente) $r->empalme('docente', 'Elige el docente.');
        if (! empty($datos['aula_id']) && ! $aula) $r->empalme('aula', 'El aula elegida no existe.');
        if (! isset($this->dias[$dia])) $r->empalme('dia', 'Elige un día de lunes a viernes.');
        if ($inicio === null || $fin === null) {
            $r->empalme('horas', 'Escribe la hora de inicio y la de fin (HH:MM).');
        } elseif ($fin <= $inicio) {
            $r->empalme('horas', 'La hora de fin debe ser posterior a la de inicio.');
        }

        if (! $r->sePuedeGuardar()) {
            return $r;
        }

        $nombreDia = $this->dias[$dia];
        $cuando = "el {$nombreDia} de " . Hora::aTexto($inicio) . ' a ' . Hora::aTexto($fin);
        $jornada = Jornada::actual();

        // 2. Que todo esté activo y sea coherente
        if (! $grupo->activo) $r->empalme('grupo_baja', "El grupo {$grupo->nombre} está dado de baja.");
        if ((int) $grupo->semestre_id !== (int) $semestre->id) $r->empalme('grupo_periodo', "El grupo {$grupo->nombre} no es del semestre {$semestre->nombre}.");
        if (! $materia->activo) $r->empalme('materia_baja', "La materia {$materia->nombre} está dada de baja.");
        if (! $docente->activo) $r->empalme('docente_baja', "El docente {$docente->nombre_completo} está dado de baja.");
        if ($aula && ! $aula->activo) $r->empalme('aula_baja', "El aula {$aula->clave} está dada de baja.");
        if ((int) $materia->semestre !== (int) $grupo->grado) {
            $r->empalme('materia_grado', "{$materia->nombre} es de {$materia->semestre}.º semestre y el grupo {$grupo->nombre} es de {$grupo->grado}.º.");
        }
        if (! $docente->materias()->whereKey($materia->id)->exists()) {
            $r->empalme('perfil', "{$docente->nombre_completo} no tiene {$materia->nombre} en su perfil. Agrégala en Docentes si sí la puede dar.");
        }

        // 3. Jornada y recesos
        if ($inicio < $jornada->entrada || $fin > $jornada->salida) {
            $r->empalme('jornada', "La clase queda fuera de la jornada ({$jornada->hora_entrada_corta}–{$jornada->hora_salida_corta}).");
        }
        foreach (Receso::orderBy('hora_inicio')->get() as $receso) {
            if ($receso->seEncimaCon($inicio, $fin)) {
                $r->empalme('receso', "Se encima con el {$receso->nombre} ({$receso->rango}).");
            }
        }

        // 4. Otras clases del mismo día y semestre
        $mismoDia = Clase::with(['grupo', 'materia', 'docente', 'aula'])
            ->where('semestre_id', $semestre->id)
            ->where('dia', $dia)
            ->when($ignorarId, fn ($q) => $q->whereKeyNot($ignorarId))
            ->get()
            ->filter(fn (Clase $c) => $c->seEncimaCon($inicio, $fin));

        foreach ($mismoDia as $otra) {
            $detalle = "{$otra->materia->nombre} de {$otra->rango}";
            if ((int) $otra->grupo_id === (int) $grupo->id) {
                $r->empalme('grupo_ocupado', "El grupo {$grupo->nombre} ya tiene {$detalle} con {$otra->docente->nombre_completo}.");
            }
            if ((int) $otra->docente_id === (int) $docente->id) {
                $r->empalme('docente_ocupado', "{$docente->nombre_completo} ya da {$detalle} al grupo {$otra->grupo->nombre}.");
            }
            if ($aula && (int) $otra->aula_id === (int) $aula->id) {
                $r->empalme('aula_ocupada', "El aula {$aula->clave} ya está ocupada por el grupo {$otra->grupo->nombre} con {$detalle}.");
            }
        }

        // 5. Disponibilidad del docente: la clase completa debe caber en uno de sus rangos
        $rangos = Disponibilidad::where('docente_id', $docente->id)->get();
        if ($rangos->isEmpty()) {
            $r->aviso('sin_disponibilidad', "{$docente->nombre_completo} no ha capturado su disponibilidad; no se pudo comprobar.");
        } else {
            $delDia = $rangos->where('dia', $dia);
            $cabe = $delDia->contains(fn ($d) => $d->inicio <= $inicio && $d->fin >= $fin);
            if (! $cabe) {
                $texto = $delDia->isEmpty()
                    ? 'no tiene disponibilidad ese día'
                    : 'solo está disponible ' . $delDia->sortBy('inicio')->map->rango->join(', ');
                $r->empalme('disponibilidad', "{$docente->nombre_completo} no está disponible {$cuando}: {$texto}.");
            }
        }

        // 6. Horas: de la materia para el grupo y de la plaza del docente
        $mph = max(1, $jornada->minutos_por_hora);
        $minutos = $fin - $inicio;

        $delGrupo = Clase::where('grupo_id', $grupo->id)->where('materia_id', $materia->id)
            ->when($ignorarId, fn ($q) => $q->whereKeyNot($ignorarId))->get()->sum->minutos;
        $limite = $materia->horas_semana * $mph;
        if ($delGrupo + $minutos > $limite) {
            $r->empalme('horas_materia', "{$materia->nombre} lleva {$materia->horas_semana} h a la semana ("
                . Hora::duracion($limite) . "). El grupo {$grupo->nombre} ya tiene " . Hora::duracion($delGrupo)
                . ' y con esta clase pasaría a ' . Hora::duracion($delGrupo + $minutos) . '.');
        }

        $delDocente = Clase::where('semestre_id', $semestre->id)->where('docente_id', $docente->id)
            ->when($ignorarId, fn ($q) => $q->whereKeyNot($ignorarId))->get()->sum->minutos;
        $plaza = $docente->horas_plaza * $mph;
        if ($delDocente + $minutos > $plaza) {
            $r->empalme('horas_plaza', "{$docente->nombre_completo} tiene plaza de {$docente->horas_plaza} h ("
                . Hora::duracion($plaza) . ') y ya tiene ' . Hora::duracion($delDocente) . ' asignadas.');
        }

        // 7. Avisos
        if (! $aula) {
            $r->aviso('sin_aula', 'La clase no tiene aula asignada.');
        } elseif ($aula->capacidad < $grupo->capacidad) {
            $r->aviso('aula_chica', "El aula {$aula->clave} es para {$aula->capacidad} y el grupo es de {$grupo->capacidad}.");
        }

        $mismaMateriaHoy = Clase::where('grupo_id', $grupo->id)->where('materia_id', $materia->id)->where('dia', $dia)
            ->when($ignorarId, fn ($q) => $q->whereKeyNot($ignorarId))->get()->sum->minutos + $minutos;
        if ($mismaMateriaHoy > 2 * $mph) {
            $r->aviso('mucho_mismo_dia', "El {$nombreDia} el grupo tendría " . Hora::duracion($mismaMateriaHoy) . " de {$materia->nombre} (más de 2 horas clase).");
        }

        return $r;
    }

    /**
     * Revisa todas las clases de un semestre contra todo lo demás (por si algo cambió después,
     * como un receso nuevo o una disponibilidad menor). Regresa [clase_id => Revision] solo con problemas.
     */
    public function revisarSemestre(Semestre $semestre): array
    {
        $problemas = [];

        foreach (Clase::where('semestre_id', $semestre->id)->get() as $clase) {
            $revision = $this->revisar($clase->only([
                'semestre_id', 'grupo_id', 'materia_id', 'docente_id', 'aula_id', 'dia', 'hora_inicio', 'hora_fin',
            ]), $clase->id);

            if (! $revision->sePuedeGuardar()) {
                $problemas[$clase->id] = $revision;
            }
        }

        return $problemas;
    }
}
