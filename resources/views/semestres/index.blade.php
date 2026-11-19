@extends('layouts.app')

@section('titulo', 'Semestres')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <h1>Semestres</h1>
    <a href="{{ route('semestres.create') }}" class="btn btn--primario btn--enlace">Nuevo semestre</a>
</div>

<div class="tabla-contenedor">
    <table class="tabla-catalogo">
        <thead>
            <tr><th>Nombre</th><th>Inicio</th><th>Fin</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        @forelse ($semestres as $semestre)
            <tr>
                <td>{{ $semestre->nombre }}</td>
                <td>{{ $semestre->fecha_inicio->format('d/m/Y') }}</td>
                <td>{{ $semestre->fecha_fin->format('d/m/Y') }}</td>
                <td>
                    {{-- Una sola etiqueta por fila: Activo, Terminado o ninguna --}}
                    @if ($semestre->activo)
                        <span class="etiqueta etiqueta--activa">Activo</span>
                    @elseif ($semestre->fecha_fin->isPast())
                        <span class="etiqueta etiqueta--terminado">Terminado</span>
                    @endif
                </td>
                <td class="tabla-catalogo__acciones">
                    <a href="{{ route('semestres.edit', $semestre) }}" class="btn btn--secundario btn--chico btn--enlace">Editar</a>
                    @unless ($semestre->activo)
                        <form method="POST" action="{{ route('semestres.activar', $semestre) }}"
                              onsubmit="return confirm('¿Marcar este semestre como activo? El que esté activo dejará de estarlo.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn--secundario btn--chico">Marcar como activo</button>
                        </form>
                    @endunless
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Todavía no hay semestres. Registra el primero con “Nuevo semestre”.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
