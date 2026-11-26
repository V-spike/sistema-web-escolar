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
@elseif ($grupos->isEmpty())
    <div class="mensaje mensaje--aviso">
        {{ $esAdmin ? 'No hay grupos activos en este semestre.' : 'Todavía no tienes clases asignadas en el horario de este semestre.' }}
    </div>
@endif

{{-- Un panel por grupo con sus materias --}}
@foreach ($grupos as $grupo)
    @php $asignaciones = $porGrupo[$grupo->id] ?? collect(); @endphp
    <div class="panel">
        <div class="panel__titulo">
            <h2>{{ $grupo->nombre }} <span class="tenue" style="font-weight:400">· {{ $grupo->alumnos()->where('activo', true)->count() }} alumnos</span></h2>
            <div class="barra-catalogo__acciones">
                @if ($esAdmin)
                    @foreach ($parciales as $p)
                        <a href="{{ route('calificaciones.informe', [$grupo, 'parcial' => $p]) }}" class="btn btn--secundario btn--chico btn--enlace">Informe {{ $p }}P</a>
                    @endforeach
                @endif
                @if ($esAdmin || in_array($grupo->id, $tutorDe))
                    <a href="{{ route('calificaciones.conducta', $grupo) }}" class="btn btn--secundario btn--chico btn--enlace">Conducta</a>
                @endif
                <a href="{{ route('boletas.grupo', $grupo) }}" class="btn btn--primario btn--chico btn--enlace">Boletas para imprimir</a>
            </div>
        </div>

        @if ($asignaciones->isEmpty())
            <p class="tenue" style="margin:0">Eres tutor de este grupo: aquí capturas su conducta del semestre.</p>
        @else
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
        @endif
    </div>
@endforeach
@endsection
