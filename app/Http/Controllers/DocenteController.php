<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Materia;
use App\Horarios\Guardas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocenteController extends Controller
{
    /** Opciones del selector "Ordenar por": clave => [texto, columna, dirección] */
    private const ORDENES = [
        'nombre'      => ['Nombre (A–Z)', 'apellido_paterno', 'asc'],
        'nombre_desc' => ['Nombre (Z–A)', 'apellido_paterno', 'desc'],
        'numero'      => ['Número de personal', 'numero_personal', 'asc'],
        'horas_desc'  => ['Más horas de plaza', 'horas_plaza', 'desc'],
        'horas'       => ['Menos horas de plaza', 'horas_plaza', 'asc'],
    ];

    public function index(Request $request)
    {
        // Lo que eligió la persona; si manda algo raro se usa el orden por nombre
        $orden = array_key_exists($request->query('orden'), self::ORDENES) ? $request->query('orden') : 'nombre';
        $estado = in_array($request->query('estado'), ['activos', 'baja'], true) ? $request->query('estado') : 'todos';
        $buscar = trim((string) $request->query('buscar'));

        [, $columna, $direccion] = self::ORDENES[$orden];

        $docentes = Docente::with('materias')
            ->when($estado === 'activos', fn ($q) => $q->where('activo', true))
            ->when($estado === 'baja', fn ($q) => $q->where('activo', false))
            ->when($buscar !== '', fn ($q) => $q->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                    ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                    ->orWhere('numero_personal', 'like', "%{$buscar}%");
            }))
            ->orderByDesc('activo')              // los dados de baja siempre al final
            ->orderBy($columna, $direccion)
            ->orderBy('apellido_paterno')        // desempate
            ->orderBy('apellido_materno')
            ->get();

        return view('docentes.index', [
            'docentes' => $docentes,
            'ordenes' => self::ORDENES,
            'orden' => $orden,
            'estado' => $estado,
            'buscar' => $buscar,
        ]);
    }

    public function create()
    {
        $materias = Materia::where('activo', true)
            ->where('tipo', '!=', 'bloque_comun')
            ->orderBy('semestre')
            ->orderBy('nombre')
            ->get();

        return view('docentes.crear', compact('materias'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'numero_personal'  => ['required', 'string', 'max:20', 'unique:docentes,numero_personal'],
            'nombre'           => ['required', 'string', 'max:60'],
            'apellido_paterno' => ['required', 'string', 'max:60'],
            'apellido_materno' => ['nullable', 'string', 'max:60'],
            'curp'             => ['nullable', 'string', 'size:18', 'unique:docentes,curp'],
            'rfc'              => ['nullable', 'string', 'max:13'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'correo'           => ['nullable', 'email', 'max:100'],
            'categoria'        => ['nullable', 'string', 'max:50'],
            'sindicato'        => ['nullable', 'string', 'max:30'],
            'licenciatura'     => ['nullable', 'string', 'max:150'],
            'especialidad'     => ['nullable', 'string', 'max:150'],
            'maestria'         => ['nullable', 'string', 'max:150'],
            'doctorado'        => ['nullable', 'string', 'max:150'],
            'cedula'           => ['nullable', 'string', 'max:20'],
            'horas_plaza'      => ['required', 'integer', 'between:1,40'],
            'materias'         => ['nullable', 'array'],
            'materias.*'       => ['exists:materias,id'],
        ]);

        $docente = Docente::create(collect($datos)->except('materias')->all());
        $docente->materias()->sync($request->input('materias', []));

        return redirect()->route('docentes.index')->with('estado', 'Docente registrado correctamente.');
    }

    public function edit(Docente $docente)
    {
        $docente->load('materias');

        $materias = Materia::where('activo', true)
            ->where('tipo', '!=', 'bloque_comun')
            ->orderBy('semestre')
            ->orderBy('nombre')
            ->get();

        return view('docentes.editar', compact('docente', 'materias'));
    }

    public function update(Request $request, Docente $docente)
    {
        $datos = $request->validate([
            'numero_personal'  => ['required', 'string', 'max:20', Rule::unique('docentes', 'numero_personal')->ignore($docente->id)],
            'nombre'           => ['required', 'string', 'max:60'],
            'apellido_paterno' => ['required', 'string', 'max:60'],
            'apellido_materno' => ['nullable', 'string', 'max:60'],
            'curp'             => ['nullable', 'string', 'size:18', Rule::unique('docentes', 'curp')->ignore($docente->id)],
            'rfc'              => ['nullable', 'string', 'max:13'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'correo'           => ['nullable', 'email', 'max:100'],
            'categoria'        => ['nullable', 'string', 'max:50'],
            'sindicato'        => ['nullable', 'string', 'max:30'],
            'licenciatura'     => ['nullable', 'string', 'max:150'],
            'especialidad'     => ['nullable', 'string', 'max:150'],
            'maestria'         => ['nullable', 'string', 'max:150'],
            'doctorado'        => ['nullable', 'string', 'max:150'],
            'cedula'           => ['nullable', 'string', 'max:20'],
            'horas_plaza'      => ['required', 'integer', 'between:1,40'],
            'materias'         => ['nullable', 'array'],
            'materias.*'       => ['exists:materias,id'],
        ]);

        $materias = array_map('intval', $request->input('materias', []));
        if ($causa = Guardas::cambioDocente($docente, $materias, (int) $datos['horas_plaza'])) {
            return back()->withInput()->withErrors(['materias' => $causa]);
        }

        $docente->update(collect($datos)->except('materias')->all());
        $docente->materias()->sync($request->input('materias', []));

        // Si tiene cuenta para entrar, su nombre se mantiene igual al del docente
        $docente->usuario?->update(['name' => $docente->nombre_completo]);

        return redirect()->route('docentes.index')->with('estado', 'Docente actualizado correctamente.');
    }

    public function cambiarEstado(Docente $docente)
    {
        if ($docente->activo && $causa = Guardas::bajaDocente($docente)) {
            return redirect()->route('docentes.index')->withErrors(['estado' => $causa]);
        }

        $docente->update(['activo' => ! $docente->activo]);

        // Su cuenta para entrar sigue el mismo estado: si se da de baja, ya no entra
        $docente->usuario?->update(['activo' => $docente->activo]);

        return redirect()->route('docentes.index')->with('estado', 'Estado del docente actualizado.');
    }
}