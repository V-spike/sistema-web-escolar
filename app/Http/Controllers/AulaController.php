<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Aula;

class AulaController extends Controller
{
    public function store(Request $request)
{
    $datos = $request->validate([
        'clave' => ['required', 'string', 'max:20', 'unique:aulas,clave'],
        'edificio' => ['required', 'string', 'max:50'],
        'capacidad' => ['required', 'integer', 'between:1,200'],
    ]);

    Aula::create($datos);

    return redirect()->route('aulas.index')->with('estado', 'Aula registrada');
}

public function update(Request $request, Aula $aula)
{
    $datos = $request->validate([
        'clave' => ['required', 'string', 'max:20', Rule::unique('aulas', 'clave')->ignore($aula->id)],
        'edificio' => ['required', 'string', 'max:50'],
        'capacidad' => ['required', 'integer', 'between:1,200'],
    ]);

    $aula->update($datos);

    return redirect()->route('aulas.index')->with('estado', 'Aula actualizada');
}
}

