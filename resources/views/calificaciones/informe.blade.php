@extends('layouts.app')

@section('titulo', 'Informe de calificaciones')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
@use('App\Calificaciones\Boleta')
@php $escuela = config('escolar.escuela'); @endphp

<div class="barra-catalogo no-imprimir">
    <div>
        <h1>Informe {{ $grupo->nombre }} · parcial {{ $parcial }}</h1>
        <p class="tenue" style="margin:0">Mismo formato que la hoja "Informe de calificaciones" de la escuela.</p>
    </div>
    <div class="barra-catalogo__acciones">
        <div class="pestanas">
            @foreach (range(1, config('escolar.parciales')) as $p)
                <a href="{{ route('calificaciones.informe', [$grupo, 'parcial' => $p]) }}" @class(['activa' => $p === $parcial])>{{ $p }}P</a>
            @endforeach
        </div>
        <button type="button" class="btn btn--primario btn--enlace" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
</div>

<div class="hoja-oficial">
    {{-- Encabezado del plantel --}}
    <table class="formato">
        <tr>
            <th colspan="2">{{ $escuela['direccion_general'] }}</th>
            <th colspan="4" class="formato__titulo">Informe de calificaciones</th>
        </tr>
        <tr>
            <td class="formato__etiqueta">Nombre del plantel</td><td>{{ $escuela['nombre'] }} · {{ $escuela['turno'] }}</td>
            <td class="formato__etiqueta">Clave</td><td>{{ $escuela['clave'] }}</td>
            <td class="formato__etiqueta">Localidad</td><td>{{ $escuela['municipio'] }}</td>
        </tr>
        <tr>
            <td class="formato__etiqueta">Periodo escolar</td><td>{{ $semestre->nombre }}</td>
            <td class="formato__etiqueta">Semestre</td><td>{{ Boleta::romano($grupo->grado) }}</td>
            <td class="formato__etiqueta">Tipo de calificación · Grupo</td><td>{{ $parcial }}P · {{ $grupo->letra }}</td>
        </tr>
    </table>

    {{-- Alumnos × materias --}}
    <table class="formato formato--datos">
        <thead>
            <tr>
                <th>No</th><th>Nombre completo del educando</th>
                @foreach ($materias as $materia) <th>{{ $materia->clave_corta }}</th> @endforeach
            </tr>
        </thead>
        <tbody>
        @forelse ($alumnos as $i => $alumno)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="formato__nombre">{{ mb_strtoupper($alumno->name) }}</td>
                @foreach ($materias as $materia)
                    <td>{{ $valores[$alumno->id][$materia->id] ?? '-' }}</td>
                @endforeach
            </tr>
        @empty
            <tr><td colspan="{{ $materias->count() + 2 }}">Este grupo no tiene alumnos activos.</td></tr>
        @endforelse
        </tbody>
    </table>

    @include('boletas._firmas')
</div>
@endsection
