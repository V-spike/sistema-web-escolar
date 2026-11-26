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

{{-- Filtros: se mandan por GET, así la dirección guarda el orden elegido --}}
<form method="GET" action="{{ route('docentes.index') }}" class="filtros">
    <input type="search" name="buscar" value="{{ $buscar }}" class="campo" placeholder="Buscar por nombre o número" aria-label="Buscar docente">

    <label class="filtros__opcion">
        Ordenar por
        <select name="orden" class="campo" onchange="this.form.submit()">
            @foreach ($ordenes as $clave => [$texto])
                <option value="{{ $clave }}" @selected($orden === $clave)>{{ $texto }}</option>
            @endforeach
        </select>
    </label>

    <label class="filtros__opcion">
        Mostrar
        <select name="estado" class="campo" onchange="this.form.submit()">
            <option value="todos" @selected($estado === 'todos')>Todos</option>
            <option value="activos" @selected($estado === 'activos')>Solo activos</option>
            <option value="baja" @selected($estado === 'baja')>Solo dados de baja</option>
        </select>
    </label>

    <button type="submit" class="btn btn--secundario btn--enlace">Buscar</button>
    @if ($buscar !== '' || $orden !== 'nombre' || $estado !== 'todos')
        <a href="{{ route('docentes.index') }}" class="btn btn--fantasma btn--enlace">Quitar filtros</a>
    @endif
    <span class="filtros__total tenue">{{ $docentes->count() }} {{ $docentes->count() === 1 ? 'docente' : 'docentes' }}</span>
</form>

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
                    @if (Route::has('docentes.disponibilidad'))
                        <a href="{{ route('docentes.disponibilidad', $docente) }}" class="btn btn--secundario btn--chico btn--enlace">Disponibilidad</a>
                    @endif
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
            <tr><td colspan="6">
                @if ($buscar !== '' || $estado !== 'todos')
                    Ningún docente coincide con la búsqueda.
                @else
                    Todavía no hay docentes. Registra el primero con “Nuevo docente”.
                @endif
            </td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection