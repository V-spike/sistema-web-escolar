@extends('layouts.app')

@section('titulo', 'Materias')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
@php
    $tipos = ['normal' => 'Normal', 'bloque_comun' => 'Bloque común', 'tutoria' => 'Tutoría'];
@endphp
<div class="barra-catalogo">
    <h1>Materias</h1>
    <a href="{{ route('materias.create') }}" class="btn btn--primario btn--enlace">Nueva materia</a>
</div>

<div class="tabla-contenedor">
    <table class="tabla-catalogo">
        <thead>
            <tr><th>Clave</th><th>Clave corta</th><th>Nombre</th><th>Semestre</th><th>Tipo</th><th>Horas/semana</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        @forelse ($materias as $materia)
            <tr @class(['tabla-catalogo__fila--baja' => ! $materia->activo])>
                <td>{{ $materia->clave }}</td>
                <td>{{ $materia->clave_corta ?? '—' }}</td>
                <td>{{ $materia->nombre }}</td>
                <td>{{ $materia->semestre }}.º</td>
                <td>{{ $tipos[$materia->tipo] ?? $materia->tipo }}</td>
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
            <tr><td colspan="8">Todavía no hay materias. Registra la primera con “Nueva materia”.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection