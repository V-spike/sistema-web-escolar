@extends('layouts.app')

@section('titulo', 'Editar materia')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Editar materia</h1>

<form method="POST" action="{{ route('materias.update', $materia) }}" class="formulario">
    @csrf
    @method('PUT')

    <label for="clave" class="campo__etiqueta">Clave</label>
    <input id="clave" name="clave" type="text" maxlength="20" value="{{ old('clave', $materia->clave) }}"
           class="campo @error('clave') campo--error @enderror" required autofocus>
    @error('clave') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="nombre" class="campo__etiqueta">Nombre</label>
    <input id="nombre" name="nombre" type="text" maxlength="100" value="{{ old('nombre', $materia->nombre) }}"
           class="campo @error('nombre') campo--error @enderror" required>
    @error('nombre') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="horas_semana" class="campo__etiqueta">Horas por semana</label>
    <input id="horas_semana" name="horas_semana" type="number" min="1" max="40" value="{{ old('horas_semana', $materia->horas_semana) }}"
           class="campo @error('horas_semana') campo--error @enderror" required>
    @error('horas_semana') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    {{-- Clave corta --}}
    <label for="clave_corta">Clave corta</label>
    <input type="text" id="clave_corta" name="clave_corta" class="campo"
        maxlength="10" required
        value="{{ old('clave_corta', $materia->clave_corta ?? '') }}">

    {{-- Semestre --}}
    <label for="semestre">Semestre</label>
    <select id="semestre" name="semestre" class="campo" required>
        @foreach ([1 => '1.º', 2 => '2.º', 3 => '3.º', 4 => '4.º', 5 => '5.º', 6 => '6.º'] as $valor => $texto)
            <option value="{{ $valor }}" @selected((int) old('semestre', $materia->semestre ?? 1) === $valor)>{{ $texto }}</option>
        @endforeach
    </select>

    {{-- Tipo --}}
    <label for="tipo">Tipo</label>
    <select id="tipo" name="tipo" class="campo" required>
        @foreach (['normal' => 'Normal', 'bloque_comun' => 'Bloque común', 'tutoria' => 'Tutoría'] as $valor => $texto)
            <option value="{{ $valor }}" @selected(old('tipo', $materia->tipo ?? 'normal') === $valor)>{{ $texto }}</option>
        @endforeach
    </select>

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('materias.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection