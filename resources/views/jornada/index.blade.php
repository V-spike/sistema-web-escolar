@extends('layouts.app')

@section('titulo', 'Jornada')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <h1>Jornada</h1>
</div>

<p>Las franjas de la jornada escolar. Aquí solo se ajustan las horas: no se crean ni se dan de baja.</p>

<div class="tabla-contenedor">
    <table class="tabla-catalogo">
        <thead>
            <tr><th>Orden</th><th>Inicio</th><th>Fin</th><th>Tipo</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        @forelse ($franjas as $franja)
            <tr>
                <td>{{ $franja->orden }}</td>
                <td>{{ substr($franja->hora_inicio, 0, 5) }}</td>
                <td>{{ substr($franja->hora_fin, 0, 5) }}</td>
                <td>
                    @if ($franja->es_receso)
                        <span class="etiqueta etiqueta--terminado">Receso</span>
                    @else
                        <span class="etiqueta etiqueta--activa">Clase</span>
                    @endif
                </td>
                <td class="tabla-catalogo__acciones">
                    <a href="{{ route('jornada.edit', $franja) }}" class="btn btn--secundario btn--chico btn--enlace">Editar</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Todavía no hay franjas. Corre el seeder de la jornada.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
