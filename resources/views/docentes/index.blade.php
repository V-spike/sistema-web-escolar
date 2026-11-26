@extends('layouts.app')

@section('titulo', 'Docentes')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <h1>Docentes</h1>
    <a href="{{ route('docentes.create') }}" class="btn btn--primario btn--enlace">Nuevo docente</a>
</div>

<div class="tabla-contenedor">
    <table class="tabla-catalogo">
        <thead>
            <tr>
                <th>No. personal</th><th>Nombre</th><th>Horas de plaza</th>
                <th>Materias</th><th>Estado</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($docentes as $docente)
            <tr @class(['tabla-catalogo__fila--baja' => ! $docente->activo])>
                <td>{{ $docente->numero_personal }}</td>
                <td>{{ trim("{$docente->apellido_paterno} {$docente->apellido_materno} {$docente->nombre}") }}</td>
                <td>{{ $docente->horas_plaza }}</td>
                <td>{{ $docente->materias->pluck('clave_corta')->join(', ') ?: '—' }}</td>
                <td>
                    <span @class(['etiqueta', 'etiqueta--activa' => $docente->activo, 'etiqueta--baja' => ! $docente->activo])>
                        {{ $docente->activo ? 'Activo' : 'Baja' }}
                    </span>
                </td>
                <td class="tabla-catalogo__acciones">
                    <a href="{{ route('docentes.edit', $docente) }}" class="btn btn--secundario btn--chico btn--enlace">Editar</a>
                    <a href="{{ route('docentes.disponibilidad', $docente) }}" class="btn btn--secundario btn--chico btn--enlace">Disponibilidad</a>
                    <form method="POST" action="{{ route('docentes.estado', $docente) }}"
                          onsubmit="return confirm('¿Seguro que quieres cambiar el estado de este docente?')">
                        @csrf
                        
                        @method('PATCH')
                        <button type="submit" class="btn btn--chico {{ $docente->activo ? 'btn--peligro' : 'btn--secundario' }}">
                            {{ $docente->activo ? 'Dar de baja' : 'Reactivar' }}
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Todavía no hay docentes. Registra el primero con “Nuevo docente”.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection