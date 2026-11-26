@extends('layouts.app')

@section('titulo', 'Grupos')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <h1>Grupos</h1>
    <a href="{{ route('grupos.create') }}" class="btn btn--primario btn--enlace">Nuevo grupo</a>
</div>

<div class="tabla-contenedor">
    <table class="tabla-catalogo">
        <thead>
            <tr>
                <th>Grupo</th><th>Periodo</th><th>Salón</th>
                <th>Capacidad</th><th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($grupos as $grupo)
            <tr @class(['tabla-catalogo__fila--baja' => ! $grupo->activo])>
                <td>
                    {{ $grupo->grado }}.º {{ $grupo->letra }}
                    @if ($grupo->area)
                        <small>· {{ $grupo->area }}</small>
                    @endif
                </td>
                <td>{{ $grupo->semestre->nombre }}</td>
                <td>{{ $grupo->aula?->clave ?? 'Sin salón' }}</td>
                <td>{{ $grupo->capacidad }}</td>
                <td>
                    <span @class(['etiqueta', 'etiqueta--activa' => $grupo->activo, 'etiqueta--baja' => ! $grupo->activo])>
                        {{ $grupo->activo ? 'Activo' : 'Baja' }}
                    </span>
                </td>
                <td class="tabla-catalogo__acciones">
                    <a href="{{ route('grupos.edit', $grupo) }}" class="btn btn--secundario btn--chico btn--enlace">Editar</a>
                    <form method="POST" action="{{ route('grupos.estado', $grupo) }}"
                          onsubmit="return confirm('¿Seguro que quieres cambiar el estado de este grupo?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn--chico {{ $grupo->activo ? 'btn--peligro' : 'btn--secundario' }}">
                            {{ $grupo->activo ? 'Dar de baja' : 'Reactivar' }}
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Todavía no hay grupos. Registra el primero con “Nuevo grupo”.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection