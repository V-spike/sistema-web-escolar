<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocenteController extends Controller
{
    public function index()
    {
        $docentes = Docente::with('materias')
            ->orderByDesc('activo')
            ->orderBy('apellido_paterno')
            ->get();

        return view('docentes.index', compact('docentes'));
    }

    public function create()
    {
        $materias = Materia::where('activo', true)
            ->where('tipo', 'normal')
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
            'titulo'           => ['nullable', 'string', 'max:100'],
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
            ->where('tipo', 'normal')
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
            'titulo'           => ['nullable', 'string', 'max:100'],
            'cedula'           => ['nullable', 'string', 'max:20'],
            'horas_plaza'      => ['required', 'integer', 'between:1,40'],
            'materias'         => ['nullable', 'array'],
            'materias.*'       => ['exists:materias,id'],
        ]);

        $docente->update(collect($datos)->except('materias')->all());
        $docente->materias()->sync($request->input('materias', []));

        return redirect()->route('docentes.index')->with('estado', 'Docente actualizado correctamente.');
    }

    public function cambiarEstado(Docente $docente)
    {
        $docente->update(['activo' => ! $docente->activo]);

        return redirect()->route('docentes.index')->with('estado', 'Estado del docente actualizado.');
    }
}