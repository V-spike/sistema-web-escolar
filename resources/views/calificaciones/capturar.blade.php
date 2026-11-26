@extends('layouts.app')

@section('titulo', 'Capturar calificaciones')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
@php
    $parciales = range(1, config('escolar.parciales'));
    $esBloque = $materia->tipo === 'acreditable';
@endphp

<div class="barra-catalogo">
    <div>
        <h1>{{ $materia->nombre }}</h1>
        <p class="tenue" style="margin:0">{{ $grupo->nombre }} · {{ $semestre->nombre }}</p>
    </div>
    <a href="{{ route('calificaciones.index') }}" class="btn btn--secundario btn--enlace">Volver</a>
</div>

{{-- Parcial que se está capturando --}}
<div class="selector">
    <div class="pestanas">
        @foreach ($parciales as $p)
            <a href="{{ route('calificaciones.capturar', [$grupo, $materia, 'parcial' => $p]) }}" @class(['activa' => $p === $parcial])>Parcial {{ $p }}</a>
        @endforeach
    </div>
    <span class="tenue">
        {{ $esBloque ? 'Escribe A (acreditado) o N/A (no acreditado).' : 'Escribe de 5 a 10 con un decimal, o NP si no presentó.' }}
        Deja vacío para quitar la calificación. Las faltas son opcionales.
    </span>
</div>

@if ($errors->any())
    <div class="alerta alerta--empalme">
        <h3>No se guardó nada. Revisa estos datos:</h3>
        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
@endif

@if ($alumnos->isEmpty())
    <div class="mensaje mensaje--aviso">Este grupo todavía no tiene alumnos registrados.</div>
@else
<form method="POST" action="{{ route('calificaciones.guardar', [$grupo, $materia]) }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="parcial" value="{{ $parcial }}">

    {{-- Una vez por parcial: cuántas clases se dieron (la boleta suma las de los parciales) --}}
    <div class="panel panel--compacto">
        <label for="impartidas" class="campo__etiqueta" style="margin:0">Clases impartidas en el parcial {{ $parcial }}</label>
        <input id="impartidas" name="impartidas" type="number" min="0" max="200"
               value="{{ old('impartidas', $impartidas[$parcial] ?? '') }}"
               class="campo campo--calificacion @error('impartidas') campo--error @enderror">
        <span class="campo__ayuda" style="margin:0">Las faltas de cada alumno no pueden pasar de este número.</span>
    </div>

    @php $otros = array_values(array_diff($parciales, [$parcial])); @endphp
    <div class="tabla-contenedor">
        <table class="tabla-catalogo tabla-captura">
            <thead>
                <tr>
                    <th>No.</th><th>Alumno</th><th>Matrícula</th>
                    @foreach ($otros as $p) <th class="centrado">Parcial {{ $p }}</th> @endforeach
                    <th class="centrado tabla-captura__actual">Calificación P{{ $parcial }}</th>
                    <th class="centrado tabla-captura__actual">Faltas P{{ $parcial }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($alumnos as $i => $alumno)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $alumno->name }}</td>
                    <td class="tenue">{{ $alumno->matricula }}</td>

                    {{-- Los otros parciales solo se ven: calificación y faltas --}}
                    @foreach ($otros as $p)
                        <td class="centrado tenue">
                            {{ $valores[$alumno->id][$p] ?? '—' }}
                            @if (! empty($faltas[$alumno->id][$p])) <small>· {{ $faltas[$alumno->id][$p] }} f</small> @endif
                        </td>
                    @endforeach

                    {{-- El parcial elegido se escribe --}}
                    <td class="centrado tabla-captura__actual">
                        <input type="text" name="valores[{{ $alumno->id }}]" maxlength="4" inputmode="decimal"
                               value="{{ old("valores.{$alumno->id}", $valores[$alumno->id][$parcial] ?? '') }}"
                               class="campo campo--calificacion @error("valores.{$alumno->id}") campo--error @enderror"
                               aria-label="Calificación del parcial {{ $parcial }} de {{ $alumno->name }}">
                    </td>
                    <td class="centrado tabla-captura__actual">
                        <input type="number" name="faltas[{{ $alumno->id }}]" min="0" max="200"
                               value="{{ old("faltas.{$alumno->id}", $faltas[$alumno->id][$parcial] ?? '') }}"
                               class="campo campo--calificacion @error("faltas.{$alumno->id}") campo--error @enderror"
                               aria-label="Faltas del parcial {{ $parcial }} de {{ $alumno->name }}">
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar parcial {{ $parcial }}</button>
    </div>
</form>
@endif
@endsection
