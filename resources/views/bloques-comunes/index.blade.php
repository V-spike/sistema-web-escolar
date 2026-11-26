@extends('layouts.app')

@section('titulo', 'Bloques comunes')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
@php $dias = config('escolar.dias'); @endphp
<h1>Bloques comunes</h1>
<p class="tenue">Horas que todos los grupos de un grado tienen al mismo tiempo. Ningún grupo de ese grado puede tener otra clase encima.</p>

@if (! $semestre)
    <div class="mensaje mensaje--aviso">No hay un semestre activo. Activa uno en Semestres para capturar bloques.</div>
@else
<div class="rejilla-2">
    <div class="tabla-contenedor">
        <table class="tabla-catalogo">
            <thead><tr><th>Grado</th><th>Materia</th><th>Día</th><th>Horario</th><th></th></tr></thead>
            <tbody>
            @forelse ($bloques as $bloque)
                <tr>
                    <td>{{ $bloque->grado }}.º</td>
                    <td>{{ $bloque->materia->nombre }}</td>
                    <td>{{ $dias[$bloque->dia] }}</td>
                    <td>{{ $bloque->rango }}</td>
                    <td class="tabla-catalogo__acciones">
                        <form method="POST" action="{{ route('bloques-comunes.destroy', $bloque) }}" onsubmit="return confirm('¿Quitar este bloque común?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--peligro btn--chico">Quitar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Todavía no hay bloques comunes en {{ $semestre->nombre }}.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h2>Nuevo bloque</h2>
        <form method="POST" action="{{ route('bloques-comunes.store') }}">
            @csrf
            <div class="formulario__fila">
                <div>
                    <label for="grado" class="campo__etiqueta">Grado</label>
                    <select id="grado" name="grado" class="campo" required>
                        @foreach (range(1, 6) as $g)
                            <option value="{{ $g }}" @selected((int) old('grado', 1) === $g)>{{ $g }}.º</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="dia" class="campo__etiqueta">Día</label>
                    <select id="dia" name="dia" class="campo" required>
                        @foreach ($dias as $numero => $nombre)
                            <option value="{{ $numero }}" @selected((int) old('dia', 1) === $numero)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @error('grado') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
            @error('dia') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

            <label for="materia_id" class="campo__etiqueta">Materia</label>
            <select id="materia_id" name="materia_id" class="campo @error('materia_id') campo--error @enderror" required>
                @forelse ($materias as $materia)
                    <option value="{{ $materia->id }}" @selected((int) old('materia_id') === $materia->id)>{{ $materia->nombre }}</option>
                @empty
                    <option value="">No hay materias de tipo bloque común</option>
                @endforelse
            </select>
            @error('materia_id') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

            <div class="formulario__fila">
                <div>
                    <label for="hora_inicio" class="campo__etiqueta">Inicio</label>
                    <input id="hora_inicio" name="hora_inicio" type="time" class="campo @error('hora_inicio') campo--error @enderror" value="{{ old('hora_inicio') }}" required>
                </div>
                <div>
                    <label for="hora_fin" class="campo__etiqueta">Fin</label>
                    <input id="hora_fin" name="hora_fin" type="time" class="campo @error('hora_fin') campo--error @enderror" value="{{ old('hora_fin') }}" required>
                </div>
            </div>
            @error('hora_inicio') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
            @error('hora_fin') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

            <div class="formulario__acciones">
                <button type="submit" class="btn btn--primario">Agregar bloque</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
