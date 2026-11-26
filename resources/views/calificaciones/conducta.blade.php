@extends('layouts.app')

@section('titulo', 'Conducta')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
{{-- Conducta del semestre (sale marcada en la boleta). La captura el tutor del grupo o la administración. --}}
<div class="barra-catalogo">
    <div>
        <h1>Conducta de {{ $grupo->nombre }}</h1>
        <p class="tenue" style="margin:0">{{ $semestre->nombre }} · se marca en la boleta como Buena, Regular o Mala</p>
    </div>
    <a href="{{ route('calificaciones.index') }}" class="btn btn--secundario btn--enlace">Volver</a>
</div>

@if ($alumnos->isEmpty())
    <div class="mensaje mensaje--aviso">Este grupo todavía no tiene alumnos registrados.</div>
@else
<form method="POST" action="{{ route('calificaciones.conducta.guardar', $grupo) }}">
    @csrf
    @method('PUT')

    <div class="tabla-contenedor">
        <table class="tabla-catalogo">
            <thead>
                <tr><th>No.</th><th>Alumno</th>@foreach (App\Models\Conducta::VALORES as $texto) <th class="centrado">{{ $texto }}</th> @endforeach <th class="centrado">Sin capturar</th></tr>
            </thead>
            <tbody>
            @foreach ($alumnos as $i => $alumno)
                @php $actual = old("conducta.{$alumno->id}", $conductas[$alumno->id] ?? ''); @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $alumno->name }}</td>
                    @foreach (App\Models\Conducta::VALORES as $valor => $texto)
                        <td class="centrado">
                            <input type="radio" name="conducta[{{ $alumno->id }}]" value="{{ $valor }}" @checked($actual === $valor)
                                   aria-label="{{ $texto }} para {{ $alumno->name }}">
                        </td>
                    @endforeach
                    <td class="centrado">
                        <input type="radio" name="conducta[{{ $alumno->id }}]" value="" @checked($actual === '') aria-label="Sin capturar para {{ $alumno->name }}">
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar conducta</button>
    </div>
</form>
@endif
@endsection
