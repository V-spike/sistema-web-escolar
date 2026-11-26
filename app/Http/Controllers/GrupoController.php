<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Grupo;
use App\Models\Semestre;
use App\Horarios\Guardas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::with(['semestre', 'aula'])
            ->orderByDesc('activo')
            ->orderBy('grado')
            ->orderBy('letra')
            ->get();

        return view('grupos.index', compact('grupos'));
    }

    public function create()
    {
        $semestres = Semestre::orderByDesc('activo')->orderByDesc('id')->get();
        $semestreActivo = Semestre::where('activo', true)->first();
        $aulas = Aula::where('activo', true)->orderBy('clave')->get();

        return view('grupos.crear', compact('semestres', 'semestreActivo', 'aulas'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'letra' => strtoupper((string) $request->input('letra')),
        ]);

        $datos = $request->validate([
            'semestre_id' => ['required', 'exists:semestres,id'],
            'grado'       => ['required', 'integer', 'between:1,6'],
            'letra'       => [
                'required',
                'string',
                'size:1',
                'alpha',
                Rule::unique('grupos', 'letra')->where(function ($query) use ($request) {
                    return $query->where('semestre_id', $request->input('semestre_id'))
                                 ->where('grado', $request->input('grado'));
                }),
            ],
            'aula_id'     => ['nullable', 'exists:aulas,id'],
            'capacidad'   => ['required', 'integer', 'between:1,50'],
            'area'        => ['nullable', 'string', 'max:60'],
        ]);

        Grupo::create($datos);

        return redirect()->route('grupos.index')->with('estado', 'Grupo registrado correctamente.');
    }

    public function edit(Grupo $grupo)
    {
        $semestres = Semestre::orderByDesc('activo')->orderByDesc('id')->get();
        $aulas = Aula::where('activo', true)->orderBy('clave')->get();

        return view('grupos.editar', compact('grupo', 'semestres', 'aulas'));
    }

    public function update(Request $request, Grupo $grupo)
    {
        $request->merge([
            'letra' => strtoupper((string) $request->input('letra')),
        ]);

        $datos = $request->validate([
            'semestre_id' => ['required', 'exists:semestres,id'],
            'grado'       => ['required', 'integer', 'between:1,6'],
            'letra'       => [
                'required',
                'string',
                'size:1',
                'alpha',
                Rule::unique('grupos', 'letra')
                    ->where(function ($query) use ($request) {
                        return $query->where('semestre_id', $request->input('semestre_id'))
                                     ->where('grado', $request->input('grado'));
                    })
                    ->ignore($grupo->id),
            ],
            'aula_id'     => ['nullable', 'exists:aulas,id'],
            'capacidad'   => ['required', 'integer', 'between:1,50'],
            'area'        => ['nullable', 'string', 'max:60'],
        ]);

        if ($causa = Guardas::cambioGrupo($grupo, (int) $datos['grado'], (int) $datos['semestre_id'])) {
            return back()->withInput()->withErrors(['grado' => $causa]);
        }

        $grupo->update($datos);

        return redirect()->route('grupos.index')->with('estado', 'Grupo actualizado correctamente.');
    }

    public function cambiarEstado(Grupo $grupo)
    {
        if ($grupo->activo && $causa = Guardas::bajaGrupo($grupo)) {
            return redirect()->route('grupos.index')->withErrors(['estado' => $causa]);
        }

        $grupo->update(['activo' => ! $grupo->activo]);

        return redirect()->route('grupos.index')->with('estado', 'Estado del grupo actualizado.');
    }
}