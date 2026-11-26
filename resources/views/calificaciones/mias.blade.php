@extends('layouts.app')

@section('titulo', 'Mis calificaciones')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
{{-- El alumno ve sus materias y la calificación de cada parcial. La boleta oficial la imprime la escuela. --}}
@php $parciales = range(1, config('escolar.parciales')); @endphp

<div class="barra-catalogo">
    <div>
        <h1>Mis calificaciones</h1>
        @if ($boleta)
            <p class="tenue" style="margin:0">{{ auth()->user()->name }} · grupo {{ $boleta->alumno->grupo->nombre }} · {{ $boleta->semestre->nombre }}</p>
        @endif
    </div>
</div>

@if (! $boleta)
    <div class="mensaje mensaje--aviso">{{ $aviso }}</div>
@else
    <div class="tabla-contenedor">
        <table class="tabla-catalogo tabla-calificaciones">
            <thead>
                <tr>
                    <th>Materia</th>
                    @foreach ($parciales as $p) <th>Parcial {{ $p }}</th> @endforeach
                    <th>Final</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($boleta->filas as $fila)
                <tr>
                    <td>{{ $fila['materia']->nombre }}</td>
                    @foreach ($parciales as $p)
                        @php
                            $valor = $fila['parciales'][$p];
                            $reprobada = $valor !== null && (in_array($valor, ['NP', 'N/A'], true) || (is_numeric($valor) && $valor < 6));
                        @endphp
                        <td @class(['calif', 'calif--baja' => $reprobada, 'calif--vacia' => $valor === null])>{{ $valor ?? '—' }}</td>
                    @endforeach
                    <td class="calif calif--final">{{ $fila['final_texto'] ?: '—' }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Promedio</td>
                    @foreach ($parciales as $p) <td class="calif">{{ $boleta->promedioParcial[$p] ?? '—' }}</td> @endforeach
                    <td class="calif calif--final">{{ $boleta->promedioGeneral ?? '—' }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <p class="tenue" style="margin-top:.75rem">
        "—" quiere decir que aún no hay calificación. NP: no presentó (cuenta como 5). Currículum Ampliado: A acreditado, N/A no acreditado.
        La calificación final aparece cuando están los {{ count($parciales) }} parciales.
    </p>
@endif
@endsection
