@extends('layouts.app')

@section('titulo', 'Capturar calificaciones')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
@php
    $parciales = range(1, config('escolar.parciales'));
    $esBloque = $materia->tipo === 'bloque_comun';
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
        Deja vacío para quitar la calificación.
    </span>
</div>

@if ($errors->any())
    <div class="alerta alerta--empalme">
        <h3>No se guardó nada. Revisa estas calificaciones:</h3>
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

    <div class="tabla-contenedor">
        <table class="tabla-catalogo tabla-captura">
            <thead>
                <tr>
                    <th>No.</th><th>Alumno</th><th>Matrícula</th>
                    @foreach ($parciales as $p) <th>Parcial {{ $p }}</th> @endforeach
                </tr>
            </thead>
            <tbody>
            @foreach ($alumnos as $i => $alumno)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $alumno->name }}</td>
                    <td class="tenue">{{ $alumno->matricula }}</td>
                    @foreach ($parciales as $p)
                        <td>
                            @if ($p === $parcial)
                                {{-- Solo el parcial elegido se puede escribir --}}
                                <input type="text" name="valores[{{ $alumno->id }}]" maxlength="4" inputmode="decimal"
                                       value="{{ old("valores.{$alumno->id}", $valores[$alumno->id][$p] ?? '') }}"
                                       class="campo campo--calificacion @error("valores.{$alumno->id}") campo--error @enderror"
                                       aria-label="Parcial {{ $p }} de {{ $alumno->name }}">
                            @else
                                <span class="tenue">{{ $valores[$alumno->id][$p] ?? '—' }}</span>
                            @endif
                        </td>
                    @endforeach
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
