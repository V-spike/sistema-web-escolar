<?php

namespace App\Http\Controllers;

use App\Horarios\Hora;
use App\Horarios\RevisorEmpalmes;
use App\Horarios\Semana;
use App\Models\Aula;
use App\Models\BloqueComun;
use App\Models\Clase;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Jornada;
use App\Models\Materia;
use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Acomodar el horario a mano. Cada clase se revisa con RevisorEmpalmes antes de guardarse:
 * si se encima con algo, no se guarda y se explica la causa.
 */
class HorarioController extends Controller
{
    public function __construct(private RevisorEmpalmes $revisor)
    {
    }

    /**
     * Vista de la semana de un grupo, un docente o un aula, con el formulario para agregar clases.
     */
    public function index(Request $request)
    {
        $semestre = Semestre::activo();
        if (! $semestre) {
            return view('horarios.sin-semestre');
        }

        $vista = in_array($request->query('vista'), ['grupo', 'docente', 'aula'], true) ? $request->query('vista') : 'grupo';

        $grupos = Grupo::where('semestre_id', $semestre->id)->where('activo', true)->orderBy('grado')->orderBy('letra')->get();
        $docentes = Docente::where('activo', true)->with('materias:id')->orderBy('apellido_paterno')->orderBy('nombre')->get();
        $aulas = Aula::where('activo', true)->orderBy('clave')->get();

        $lista = match ($vista) { 'docente' => $docentes, 'aula' => $aulas, default => $grupos };
        $elegido = $lista->firstWhere('id', (int) $request->query('id')) ?? $lista->first();

        $problemas = $this->revisor->revisarSemestre($semestre);
        $semana = new Semana();
        $carga = collect();
        $materias = collect();

        if ($elegido) {
            $clases = Clase::with(['grupo', 'materia', 'docente', 'aula'])
                ->where('semestre_id', $semestre->id)
                ->where($vista . '_id', $elegido->id)
                ->get();

            $semana->conClases($clases, $vista, $problemas);

            if ($vista === 'grupo') {
                $semana->conBloquesComunes(BloqueComun::with('materia')
                    ->where('semestre_id', $semestre->id)->where('grado', $elegido->grado)->get());

                $materias = Materia::where('activo', true)->where('semestre', $elegido->grado)
                    ->where('tipo', '!=', 'bloque_comun')->orderBy('nombre')->get();
                $carga = $this->carga($elegido, $materias, $clases);
            }
        }

        $problemasLista = Clase::with(['grupo', 'materia', 'docente'])->whereIn('id', array_keys($problemas))->get()
            ->map(fn ($c) => ['clase' => $c, 'revision' => $problemas[$c->id]]);

        return view('horarios.index', [
            'semestre' => $semestre,
            'vista' => $vista,
            'lista' => $lista,
            'elegido' => $elegido,
            'semana' => $semana,
            'grupos' => $grupos,
            'docentes' => $docentes,
            'aulas' => $aulas,
            'materias' => $materias,
            'carga' => $carga,
            'problemas' => $problemasLista,
            'jornada' => Jornada::actual(),
        ]);
    }

    /**
     * Revisión en vivo mientras la persona llena el formulario (no guarda nada).
     */
    public function revisar(Request $request)
    {
        $datos = $this->datos($request);

        return response()->json($this->revisor->revisar($datos, $request->integer('ignorar') ?: null)->toArray());
    }

    public function store(Request $request)
    {
        $datos = $this->datos($request);
        $this->validarFormato($request);

        $resultado = $this->guardar($datos);

        if (! $resultado['ok']) {
            return back()->withInput()
                ->with('empalmes', $resultado['revision']->empalmes)
                ->with('avisos', $resultado['revision']->avisos);
        }

        return redirect()
            ->route('horarios.index', ['vista' => 'grupo', 'id' => $datos['grupo_id']])
            ->with('estado', 'Clase agregada sin empalmes.')
            ->with('avisos', $resultado['revision']->avisos);
    }

    public function edit(Clase $clase)
    {
        $clase->load(['grupo', 'materia', 'docente', 'aula']);

        return view('horarios.editar', [
            'clase' => $clase,
            'materias' => Materia::where('activo', true)->where('semestre', $clase->grupo->grado)
                ->where('tipo', '!=', 'bloque_comun')->orderBy('nombre')->get(),
            'docentes' => Docente::where('activo', true)->with('materias:id')->orderBy('apellido_paterno')->get(),
            'aulas' => Aula::where('activo', true)->orderBy('clave')->get(),
            'revision' => $this->revisor->revisar($clase->only([
                'semestre_id', 'grupo_id', 'materia_id', 'docente_id', 'aula_id', 'dia', 'hora_inicio', 'hora_fin',
            ]), $clase->id),
        ]);
    }

    public function update(Request $request, Clase $clase)
    {
        $datos = $this->datos($request, $clase);
        $this->validarFormato($request);

        $resultado = $this->guardar($datos, $clase);

        if (! $resultado['ok']) {
            return back()->withInput()
                ->with('empalmes', $resultado['revision']->empalmes)
                ->with('avisos', $resultado['revision']->avisos);
        }

        return redirect()
            ->route('horarios.index', ['vista' => 'grupo', 'id' => $clase->grupo_id])
            ->with('estado', 'Clase actualizada sin empalmes.')
            ->with('avisos', $resultado['revision']->avisos);
    }

    public function destroy(Clase $clase)
    {
        $grupo = $clase->grupo_id;
        $clase->delete();

        return redirect()
            ->route('horarios.index', ['vista' => 'grupo', 'id' => $grupo])
            ->with('estado', 'Clase quitada del horario.');
    }

    /**
     * Guarda dentro de una transacción y con las filas del grupo, docente y aula bloqueadas:
     * si dos personas guardan al mismo tiempo, la segunda espera y su revisión ya ve la primera clase.
     */
    private function guardar(array $datos, ?Clase $clase = null): array
    {
        return DB::transaction(function () use ($datos, $clase) {
            Grupo::whereKey($datos['grupo_id'])->lockForUpdate()->first();
            Docente::whereKey($datos['docente_id'])->lockForUpdate()->first();
            if (! empty($datos['aula_id'])) {
                Aula::whereKey($datos['aula_id'])->lockForUpdate()->first();
            }

            $revision = $this->revisor->revisar($datos, $clase?->id);

            if (! $revision->sePuedeGuardar()) {
                return ['ok' => false, 'revision' => $revision];
            }

            $clase ? $clase->update($datos) : Clase::create($datos);

            return ['ok' => true, 'revision' => $revision];
        });
    }

    private function datos(Request $request, ?Clase $clase = null): array
    {
        return [
            'semestre_id' => $clase?->semestre_id ?? Semestre::activo()?->id,
            'grupo_id' => $clase?->grupo_id ?? $request->integer('grupo_id'),
            'materia_id' => $request->integer('materia_id'),
            'docente_id' => $request->integer('docente_id'),
            'aula_id' => $request->integer('aula_id') ?: null,
            'dia' => $request->integer('dia'),
            'hora_inicio' => $this->hora($request->input('hora_inicio')),
            'hora_fin' => $this->hora($request->input('hora_fin')),
        ];
    }

    private function validarFormato(Request $request): void
    {
        $request->validate([
            'materia_id' => ['required', 'integer'],
            'docente_id' => ['required', 'integer'],
            'aula_id' => ['nullable', 'integer'],
            'dia' => ['required', 'integer', 'between:1,5'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i'],
        ], [
            'hora_inicio.date_format' => 'Escribe la hora de inicio como HH:MM.',
            'hora_fin.date_format' => 'Escribe la hora de fin como HH:MM.',
        ]);
    }

    private function hora(?string $valor): ?string
    {
        $minutos = Hora::aMinutos($valor);

        return $minutos === null ? null : Hora::aTexto($minutos);
    }

    /** Horas asignadas contra horas que pide cada materia del grado */
    private function carga(Grupo $grupo, $materias, $clases)
    {
        $mph = max(1, Jornada::actual()->minutos_por_hora);

        return $materias->map(function ($materia) use ($clases, $mph) {
            $asignados = $clases->where('materia_id', $materia->id)->sum->minutos;
            $requeridos = $materia->horas_semana * $mph;

            return [
                'materia' => $materia,
                'asignadas' => round($asignados / $mph, 1),
                'requeridas' => $materia->horas_semana,
                'completa' => $asignados >= $requeridos,
            ];
        });
    }
}
