@extends('layouts.app')

@section('titulo', 'Administradores')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <h1>Administradores</h1>
    <a href="{{ route('administradores.create') }}" class="btn btn--primario btn--enlace">Nuevo administrador</a>
</div>

<div class="tabla-contenedor">
    <table class="tabla-catalogo">
        <thead>
            <tr><th>Nombre</th><th>Correo</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        @forelse ($administradores as $administrador)
            <tr @class(['tabla-catalogo__fila--baja' => ! $administrador->activo])>
                <td>{{ $administrador->name }}</td>
                <td>{{ $administrador->email }}</td>
                <td>
                    <span @class(['etiqueta', 'etiqueta--activa' => $administrador->activo, 'etiqueta--baja' => ! $administrador->activo])>
                        {{ $administrador->activo ? 'Activo' : 'Baja' }}
                    </span>
                </td>
                <td class="tabla-catalogo__acciones">
                    <a href="{{ route('administradores.edit', $administrador) }}" class="btn btn--secundario btn--chico btn--enlace">Editar</a>
                    <form method="POST" action="{{ route('administradores.estado', $administrador) }}"
                          onsubmit="return confirm('¿Seguro que quieres cambiar el estado de este administrador?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn--chico {{ $administrador->activo ? 'btn--peligro' : 'btn--secundario' }}">
                            {{ $administrador->activo ? 'Dar de baja' : 'Reactivar' }}
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4">Todavía no hay administradores. Registra el primero con “Nuevo administrador”.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
