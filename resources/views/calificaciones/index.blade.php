@extends('layouts.app')

@section('titulo', 'Calificaciones')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
@php $parciales = range(1, config('escolar.parciales')); @endphp

<div class="barra-catalogo">
    <div>
        <h1>Calificaciones</h1>
        <p class="tenue" style="margin:0">
            {{ $semestre?->nombre ?? 'Sin semestre activo' }} · escala de 5 a 10 con un decimal o NP; Currículum Ampliado con A o N/A
        </p>
    </div>
</div>

@if (! $semestre)
    <div class="mensaje mensaje--aviso">Primero marca un semestre como activo.</div>
@elseif ($porGrupo->isEmpty())
    <div class="mensaje mensaje--aviso">
        {{ $esAdmin ? 'No hay grupos activos en este semestre.' : 'Todavía no tienes clases asignadas en el horario de este semestre.' }}
    </div>
@endif

{{-- Un panel por grupo con sus materias --}}
@foreach ($porGrupo as $asignaciones)
    @php $grupo = $asignaciones->first()['grupo']; @endphp
    <div class="panel">
        <div class="panel__titulo">
            <h2>{{ $grupo->nombre }} <span class="tenue" style="font-weight:400">· {{ $asignaciones->first()['alumnos'] }} alumnos</span></h2>
            @if ($esAdmin)
                <div class="barra-catalogo__acciones">
                    @foreach ($parciales as $p)
                        <a href="{{ route('calificaciones.informe', [$grupo, 'parcial' => $p]) }}" class="btn btn--secundario btn--chico btn--enlace">Informe {{ $p }}P</a>
                    @endforeach
                    <a href="{{ route('boletas.grupo', $grupo) }}" class="btn btn--primario btn--chico btn--enlace">Boletas del grupo</a>
                </div>
            @endif
        </div>

        <div class="tabla-contenedor">
            <table class="tabla-catalogo">
                <thead>
                    <tr>
                        <th>Materia</th>
                        @foreach ($parciales as $p) <th>Parcial {{ $p }}</th> @endforeach
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($asignaciones as $a)
                    <tr>
                        <td>{{ $a['materia']->clave_corta }} · {{ $a['materia']->nombre }}</td>
                        @foreach ($parciales as $p)
                            @php $hechas = $a['capturadas'][$p] ?? 0; @endphp
                            <td>
                                <span @class(['etiqueta', 'etiqueta--activa' => $a['alumnos'] && $hechas >= $a['alumnos'], 'etiqueta--aviso' => $hechas && $hechas < $a['alumnos']])>
                                    {{ $hechas }} / {{ $a['alumnos'] }}
                                </span>
                            </td>
                        @endforeach
                        <td class="tabla-catalogo__acciones">
                            <a href="{{ route('calificaciones.capturar', [$a['grupo'], $a['materia']]) }}" class="btn btn--secundario btn--chico btn--enlace">Capturar</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach
@endsection
