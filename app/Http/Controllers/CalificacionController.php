<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Clase;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Semestre;
use Illuminate\Http\Request;

/**
 * Captura de calificaciones por parcial.
 * El docente solo califica los grupos y materias donde da clase.
 * El administrador puede calificar cualquiera (por ejemplo Currículum Ampliado, que no tiene docente fijo).
 */
class CalificacionController extends Controller
{
    public function index()
    {
        $semestre = Semestre::activo();

        return view('calificaciones.index', [
            'semestre' => $semestre,
            'porGrupo' => $semestre ? $this->asignaciones($semestre)->groupBy(fn ($a) => $a['grupo']->id) : collect(),
            'esAdmin' => $this->esAdmin(),
        ]);
    }

    public function capturar(Request $request, Grupo $grupo, Materia $materia)
    {
        $semestre = Semestre::activo();
        abort_unless($semestre && $this->puede($grupo, $materia, $semestre), 403, 'No impartes esta materia en este grupo.');

        $parcial = $this->parcial($request);
        $alumnos = $grupo->alumnos()->where('activo', true)->orderBy('name')->get();

        // $valores[alumno_id][parcial] = "8.5"
        $valores = [];
        $guardadas = Calificacion::where('semestre_id', $semestre->id)->where('materia_id', $materia->id)
            ->whereIn('alumno_id', $alumnos->pluck('id'))->get();
        foreach ($guardadas as $c) {
            $valores[$c->alumno_id][$c->parcial] = $c->valor;
        }

        return view('calificaciones.capturar', compact('grupo', 'materia', 'semestre', 'parcial', 'alumnos', 'valores'));
    }

    public function guardar(Request $request, Grupo $grupo, Materia $materia)
    {
        $semestre = Semestre::activo();
        abort_unless($semestre && $this->puede($grupo, $materia, $semestre), 403, 'No impartes esta materia en este grupo.');

        $parcial = $this->parcial($request);
        $escritos = $request->input('valores', []);
        $alumnos = $grupo->alumnos()->where('activo', true)->get();

        // 1) Revisar todo antes de guardar: si hay un error no se guarda nada
        $errores = [];
        $limpios = [];
        foreach ($alumnos as $alumno) {
            $texto = trim((string) ($escritos[$alumno->id] ?? ''));
            if ($texto === '') {
                $limpios[$alumno->id] = null;   // vacío = sin calificación
                continue;
            }

            $valor = Calificacion::normalizar($texto, $materia);
            if ($valor === null) {
                $errores["valores.{$alumno->id}"] = $materia->tipo === 'acreditable'
                    ? "{$alumno->name}: escribe A o N/A."
                    : "{$alumno->name}: escribe un número de 5 a 10 (un decimal) o NP.";
            } else {
                $limpios[$alumno->id] = $valor;
            }
        }

        if ($errores) {
            return back()->withInput()->withErrors($errores);
        }

        // 2) Guardar (vacío borra la calificación de ese parcial)
        foreach ($limpios as $alumnoId => $valor) {
            $clave = ['semestre_id' => $semestre->id, 'alumno_id' => $alumnoId, 'materia_id' => $materia->id, 'parcial' => $parcial];

            if ($valor === null) {
                Calificacion::where($clave)->delete();
            } else {
                Calificacion::updateOrCreate($clave, ['valor' => $valor]);
            }
        }

        return redirect()->route('calificaciones.capturar', [$grupo, $materia, 'parcial' => $parcial])
            ->with('estado', "Calificaciones del parcial {$parcial} guardadas.");
    }

    /** Informe del grupo en un parcial: alumnos × materias (como la hoja "DATOS" del formato oficial) */
    public function informe(Request $request, Grupo $grupo)
    {
        $semestre = Semestre::activo();
        abort_unless($semestre && $grupo->semestre_id === $semestre->id, 404);

        $parcial = $this->parcial($request);
        $materias = $this->materiasDelGrado($grupo->grado);
        $alumnos = $grupo->alumnos()->where('activo', true)->orderBy('name')->get();

        // $valores[alumno_id][materia_id] = "8.5"
        $valores = [];
        $guardadas = Calificacion::where('semestre_id', $semestre->id)->where('parcial', $parcial)
            ->whereIn('alumno_id', $alumnos->pluck('id'))->get();
        foreach ($guardadas as $c) {
            $valores[$c->alumno_id][$c->materia_id] = $c->valor;
        }

        if ($request->query('formato') === 'excel') {
            return $this->descargarExcel($grupo, $semestre, $parcial, $materias, $alumnos, $valores);
        }

        return view('calificaciones.informe', compact('grupo', 'semestre', 'parcial', 'materias', 'alumnos', 'valores'));
    }

    /**
     * El informe como archivo .csv, que Excel abre directo.
     * Lleva al inicio la marca "BOM" para que Excel lea bien los acentos.
     */
    private function descargarExcel(Grupo $grupo, Semestre $semestre, int $parcial, $materias, $alumnos, array $valores)
    {
        $escuela = config('escolar.escuela');
        $archivo = "informe-{$grupo->grado}{$grupo->letra}-parcial{$parcial}.csv";

        return response()->streamDownload(function () use ($escuela, $grupo, $semestre, $parcial, $materias, $alumnos, $valores) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");

            fputcsv($salida, ['INFORME DE CALIFICACIONES'], ',', '"', '');
            fputcsv($salida, ['Plantel', $escuela['nombre'] . ' · ' . $escuela['turno'], 'Clave', $escuela['clave']], ',', '"', '');
            fputcsv($salida, ['Periodo', $semestre->nombre, 'Grupo', $grupo->nombre, 'Parcial', "{$parcial}P"], ',', '"', '');
            fputcsv($salida, [], ',', '"', '');

            fputcsv($salida, array_merge(['No', 'Nombre', 'Matrícula', 'CURP'], $materias->pluck('clave_corta')->all()), ',', '"', '');
            foreach ($alumnos as $i => $alumno) {
                $fila = [$i + 1, mb_strtoupper($alumno->name), $alumno->matricula, $alumno->curp];
                foreach ($materias as $materia) {
                    $fila[] = $valores[$alumno->id][$materia->id] ?? '';
                }
                fputcsv($salida, $fila, ',', '"', '');
            }

            fclose($salida);
        }, $archivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Grupos y materias que puede calificar quien entró, y cuántas calificaciones lleva cada parcial.
     */
    private function asignaciones(Semestre $semestre)
    {
        if ($this->esAdmin()) {
            // Todas las materias de la boleta en todos los grupos del semestre
            $pares = Grupo::where('semestre_id', $semestre->id)->where('activo', true)->orderBy('grado')->orderBy('letra')->get()
                ->flatMap(fn ($g) => $this->materiasDelGrado($g->grado)->map(fn ($m) => ['grupo' => $g, 'materia' => $m]));
        } else {
            // Solo donde el docente tiene clases en el horario
            $docente = auth()->user()->docente;
            $clases = $docente
                ? Clase::with(['grupo', 'materia'])->where('semestre_id', $semestre->id)->where('docente_id', $docente->id)->get()
                : collect();

            $pares = $clases
                ->filter(fn ($c) => in_array($c->materia->tipo, Materia::EN_BOLETA, true))
                ->unique(fn ($c) => $c->grupo_id . '-' . $c->materia_id)
                ->sortBy(fn ($c) => $c->grupo->grado * 100 + ord($c->grupo->letra))
                ->map(fn ($c) => ['grupo' => $c->grupo, 'materia' => $c->materia])
                ->values();
        }

        return $pares->map(function ($par) use ($semestre) {
            $alumnos = $par['grupo']->alumnos()->where('activo', true)->pluck('id');
            $porParcial = Calificacion::where('semestre_id', $semestre->id)->where('materia_id', $par['materia']->id)
                ->whereIn('alumno_id', $alumnos)->get()->countBy('parcial');

            return $par + ['alumnos' => $alumnos->count(), 'capturadas' => $porParcial];
        });
    }

    /** ¿Quien entró puede calificar esta materia en este grupo? */
    private function puede(Grupo $grupo, Materia $materia, Semestre $semestre): bool
    {
        $correcta = $grupo->semestre_id === $semestre->id
            && $materia->semestre === $grupo->grado
            && in_array($materia->tipo, Materia::EN_BOLETA, true);

        if (! $correcta || $this->esAdmin()) {
            return $correcta;
        }

        $docente = auth()->user()->docente;

        return $docente && Clase::where('semestre_id', $semestre->id)->where('docente_id', $docente->id)
            ->where('grupo_id', $grupo->id)->where('materia_id', $materia->id)->exists();
    }

    private function materiasDelGrado(int $grado)
    {
        return Materia::where('semestre', $grado)->where('activo', true)->whereIn('tipo', Materia::EN_BOLETA)
            ->orderByRaw("tipo = 'acreditable'")->orderBy('id')->get();
    }

    /** El parcial que se pidió en la dirección (?parcial=2); si no es válido, el 1 */
    private function parcial(Request $request): int
    {
        $parcial = $request->integer('parcial', 1);

        return ($parcial >= 1 && $parcial <= config('escolar.parciales')) ? $parcial : 1;
    }

    private function esAdmin(): bool
    {
        return in_array(auth()->user()->rol, ['administrador', 'superusuario'], true);
    }
}
