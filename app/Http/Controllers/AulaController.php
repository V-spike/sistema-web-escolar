<?php

namespace App\Http\Controllers;

use App\Horarios\Guardas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Aula;

class AulaController extends Controller
{
    public function index()
    {
        // Primero las activas, luego por clave
        $aulas = Aula::orderByDesc('activo')->orderBy('clave')->get();
        return view('aulas.index', ['aulas' => $aulas]);
    }

    public function create()
    {
        return view('aulas.crear');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'clave' => ['required', 'string', 'max:20', 'unique:aulas,clave'],
            'edificio' => ['required', 'string', 'max:50'],
            'tipo' => ['required', Rule::in(Aula::TIPOS)],
            'capacidad' => ['required', 'integer', 'between:1,200'],
        ]);

        Aula::create($datos);

        return redirect()->route('aulas.index')->with('estado', 'Aula registrada');
    }

    public function edit(Aula $aula)
    {
        return view('aulas.editar', ['aula' => $aula]);
    }

    public function update(Request $request, Aula $aula)
    {
        $datos = $request->validate([
            'clave' => ['required', 'string', 'max:20', Rule::unique('aulas', 'clave')->ignore($aula->id)],
            'edificio' => ['required', 'string', 'max:50'],
            'tipo' => ['required', Rule::in(Aula::TIPOS)],
            'capacidad' => ['required', 'integer', 'between:1,200'],
        ]);

        $aula->update($datos);

        return redirect()->route('aulas.index')->with('estado', 'Aula actualizada');
    }

    /**
     * Baja lógica: no se borra, solo se marca como inactiva (o se reactiva).
     */
    public function cambiarEstado(Aula $aula)
    {
        if ($aula->activo && $causa = Guardas::bajaAula($aula)) {
            return redirect()->route('aulas.index')->withErrors(['estado' => $causa]);
        }

        $aula->activo = ! $aula->activo;
        $aula->save();

        $mensaje = $aula->activo ? 'Aula reactivada' : 'Aula dada de baja';

        return redirect()->route('aulas.index')->with('estado', $mensaje);
    }
}
