@extends('layouts.app')

@section('titulo', 'Materias')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <h1>Materias</h1>
    <a href="{{ route('materias.create') }}" class="btn btn--primario btn--enlace">Nueva materia</a>
</div>

<div class="tabla-contenedor">
    <table class="tabla-catalogo">
        <thead>
            <tr><th>Clave</th><th>Nombre</th><th>Horas/semana</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        @forelse ($materias as $materia)
            <tr @class(['tabla-catalogo__fila--baja' => ! $materia->activo])>
                <td>{{ $materia->clave }}</td>
                <td>{{ $materia->nombre }}</td>
                <td>{{ $materia->horas_semana }}</td>
                <td>
                    <span @class(['etiqueta', 'etiqueta--activa' => $materia->activo, 'etiqueta--baja' => ! $materia->activo])>
                        {{ $materia->activo ? 'Activa' : 'Baja' }}
                    </span>
                </td>
                <td class="tabla-catalogo__acciones">
                    <a href="{{ route('materias.edit', $materia) }}" class="btn btn--secundario btn--chico btn--enlace">Editar</a>
                    <form method="POST" action="{{ route('materias.estado', $materia) }}"
                          onsubmit="return confirm('¿Seguro que quieres cambiar el estado de esta materia?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn--chico {{ $materia->activo ? 'btn--peligro' : 'btn--secundario' }}">
                            {{ $materia->activo ? 'Dar de baja' : 'Reactivar' }}
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Todavía no hay materias. Registra la primera con “Nueva materia”.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection