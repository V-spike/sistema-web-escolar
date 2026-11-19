<?php

namespace App\Http\Controllers;

use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SemestreController extends Controller
{
    public function index()
    {
        // Del más nuevo al más viejo; los terminados se quedan como historial
        $semestres = Semestre::orderByDesc('fecha_inicio')->get();
        return view('semestres.index', ['semestres' => $semestres]);
    }

    public function create()
    {
        return view('semestres.crear');
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas(), $this->mensajes());

        Semestre::create($datos);

        return redirect()->route('semestres.index')->with('estado', 'Semestre registrado');
    }

    public function edit(Semestre $semestre)
    {
        return view('semestres.editar', ['semestre' => $semestre]);
    }

    public function update(Request $request, Semestre $semestre)
    {
        $datos = $request->validate($this->reglas($semestre), $this->mensajes());

        $semestre->update($datos);

        return redirect()->route('semestres.index')->with('estado', 'Semestre actualizado');
    }

    /**
     * Deja activo solo a este semestre. Las dos actualizaciones van juntas o ninguna.
     */
    public function activar(Semestre $semestre)
    {
        DB::transaction(function () use ($semestre) {
            Semestre::where('activo', true)->update(['activo' => false]);
            $semestre->update(['activo' => true]);
        });

        return redirect()->route('semestres.index')->with('estado', "Ahora el semestre activo es {$semestre->nombre}");
    }

    /**
     * Las mismas reglas para crear y editar. Al editar, el nombre único ignora al propio semestre.
     */
    private function reglas(?Semestre $semestre = null): array
    {
        $unico = Rule::unique('semestres', 'nombre');
        if ($semestre) {
            $unico->ignore($semestre->id);
        }

        return [
            'nombre' => ['required', 'string', 'max:50', $unico],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
        ];
    }

    private function mensajes(): array
    {
        return [
            'nombre.unique' => 'Ya existe un semestre con ese nombre.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ];
    }
}
