@extends('layouts.app')

@section('titulo', 'Aulas')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <h1>Aulas</h1>
    <a href="{{ route('aulas.create') }}" class="btn btn--primario btn--enlace">Nueva aula</a>
</div>

<div class="tabla-contenedor">
    <table class="tabla-catalogo">
        <thead>
            <tr><th>Clave</th><th>Edificio</th><th>Capacidad</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        @forelse ($aulas as $aula)
            <tr @class(['tabla-catalogo__fila--baja' => ! $aula->activo])>
                <td>{{ $aula->clave }}</td>
                <td>{{ $aula->edificio }}</td>
                <td>{{ $aula->capacidad }}</td>
                <td>
                    <span @class(['etiqueta', 'etiqueta--activa' => $aula->activo, 'etiqueta--baja' => ! $aula->activo])>
                        {{ $aula->activo ? 'Activa' : 'Baja' }}
                    </span>
                </td>
                <td class="tabla-catalogo__acciones">
                    <a href="{{ route('aulas.edit', $aula) }}" class="btn btn--secundario btn--chico btn--enlace">Editar</a>
                    <form method="POST" action="{{ route('aulas.estado', $aula) }}"
                          onsubmit="return confirm('¿Seguro que quieres cambiar el estado de esta aula?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn--chico {{ $aula->activo ? 'btn--peligro' : 'btn--secundario' }}">
                            {{ $aula->activo ? 'Dar de baja' : 'Reactivar' }}
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Todavía no hay aulas. Registra la primera con “Nueva aula”.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection