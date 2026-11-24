@extends('layouts.app')

@section('titulo', 'Bloques comunes')

@section('menu')
    @include('panel.menu')
@endsection

@php
    $dias = config('escolar.dias');
@endphp

@section('contenido')
<div class="barra-catalogo">
    <h1>Bloques comunes</h1>
</div>

<p>
    Materias que todo un grado toma a la misma hora.
    @if ($semestre)
        Semestre activo: <strong>{{ $semestre->nombre }}</strong>.
    @endif
</p>

@error('semestre') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

@if (! $semestre)
    <div class="mensaje mensaje--error">
        No hay un semestre activo. Primero marca uno como activo en
        <a href="{{ route('semestres.index') }}">Semestres</a>.
    </div>
@else
    <form method="POST" action="{{ route('bloques-comunes.store') }}" class="formulario">
        @csrf

        <label for="grado" class="campo__etiqueta">Grado</label>
        <select id="grado" name="grado" class="campo @error('grado') campo--error @enderror" required>
            <option value="">Elige un grado</option>
            @for ($g = 1; $g <= 6; $g++)
                <option value="{{ $g }}" @selected(old('grado') == $g)>{{ $g }}.º</option>
            @endfor
        </select>
        @error('grado') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="materia_id" class="campo__etiqueta">Materia</label>
        <select id="materia_id" name="materia_id" class="campo @error('materia_id') campo--error @enderror" required>
            <option value="">Elige una materia</option>
            @foreach ($materias as $materia)
                <option value="{{ $materia->id }}" @selected(old('materia_id') == $materia->id)>{{ $materia->clave }} · {{ $materia->nombre }}</option>
            @endforeach
        </select>
        @if ($materias->isEmpty())
            <p class="campo__ayuda">No hay materias de tipo bloque común. Regístralas en Materias.</p>
        @endif
        @error('materia_id') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="dia" class="campo__etiqueta">Día</label>
        <select id="dia" name="dia" class="campo @error('dia') campo--error @enderror" required>
            <option value="">Elige un día</option>
            @foreach ($dias as $numero => $nombreDia)
                <option value="{{ $numero }}" @selected(old('dia') == $numero)>{{ $nombreDia }}</option>
            @endforeach
        </select>
        @error('dia') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="franja_id" class="campo__etiqueta">Franja</label>
        <select id="franja_id" name="franja_id" class="campo @error('franja_id') campo--error @enderror" required>
            <option value="">Elige una franja</option>
            @foreach ($franjas as $franja)
                <option value="{{ $franja->id }}" @selected(old('franja_id') == $franja->id)>
                    {{ $franja->orden }} · {{ substr($franja->hora_inicio, 0, 5) }}–{{ substr($franja->hora_fin, 0, 5) }}
                </option>
            @endforeach
        </select>
        @error('franja_id') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <div class="formulario__acciones">
            <button type="submit" class="btn btn--primario">Agregar</button>
        </div>
    </form>
@endif

<h2>Bloques del semestre</h2>

<div class="tabla-contenedor">
    <table class="tabla-catalogo">
        <thead>
            <tr><th>Grado</th><th>Materia</th><th>Día</th><th>Hora</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        @forelse ($bloques as $bloque)
            <tr>
                <td>{{ $bloque->grado }}.º</td>
                <td>{{ $bloque->materia->clave }} · {{ $bloque->materia->nombre }}</td>
                <td>{{ $dias[$bloque->dia] ?? $bloque->dia }}</td>
                <td>{{ substr($bloque->franja->hora_inicio, 0, 5) }}–{{ substr($bloque->franja->hora_fin, 0, 5) }}</td>
                <td class="tabla-catalogo__acciones">
                    {{-- DELETE: el bloque se borra de verdad, porque es configuración y no historial --}}
                    <form method="POST" action="{{ route('bloques-comunes.destroy', $bloque) }}"
                          onsubmit="return confirm('¿Quitar este bloque común?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--chico btn--peligro">Quitar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Todavía no hay bloques comunes en este semestre.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
