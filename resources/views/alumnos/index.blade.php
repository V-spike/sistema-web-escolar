@extends('layouts.app')

@section('titulo', 'Alumnos')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <h1>Alumnos</h1>
    <div class="barra-catalogo__acciones">
        <form method="GET" action="{{ route('alumnos.index') }}">
            <select name="grupo" class="campo" style="width:auto" onchange="this.form.submit()" aria-label="Filtrar por grupo">
                <option value="">Todos los grupos</option>
                @foreach ($grupos as $grupo)
                    <option value="{{ $grupo->id }}" @selected($grupoId === $grupo->id)>{{ $grupo->nombre }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('alumnos.create') }}" class="btn btn--primario btn--enlace">Nuevo alumno</a>
    </div>
</div>

<div class="tabla-contenedor">
    <table class="tabla-catalogo">
        <thead><tr><th>Matrícula</th><th>Nombre</th><th>Correo</th><th>Grupo</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
        @forelse ($alumnos as $alumno)
            <tr @class(['tabla-catalogo__fila--baja' => ! $alumno->activo])>
                <td>{{ $alumno->matricula ?? '—' }}</td>
                <td>{{ $alumno->name }}</td>
                <td>{{ $alumno->email }}</td>
                <td>{{ $alumno->grupo?->nombre ?? 'Sin grupo' }}</td>
                <td><span @class(['etiqueta', 'etiqueta--activa' => $alumno->activo, 'etiqueta--baja' => ! $alumno->activo])>{{ $alumno->activo ? 'Activo' : 'Baja' }}</span></td>
                <td class="tabla-catalogo__acciones">
                    <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn--secundario btn--chico btn--enlace">Editar</a>
                    <a href="{{ route('boletas.alumno', $alumno) }}" class="btn btn--secundario btn--chico btn--enlace">Boleta</a>
                    <form method="POST" action="{{ route('alumnos.estado', $alumno) }}" onsubmit="return confirm('¿Seguro que quieres cambiar el estado de este alumno?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn--chico {{ $alumno->activo ? 'btn--peligro' : 'btn--secundario' }}">{{ $alumno->activo ? 'Dar de baja' : 'Reactivar' }}</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No hay alumnos{{ $grupoId ? ' en ese grupo' : '' }}. Registra el primero con “Nuevo alumno”.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
