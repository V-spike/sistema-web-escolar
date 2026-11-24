@extends('layouts.app')

@section('titulo', 'Nueva materia')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Nueva materia</h1>

<form method="POST" action="{{ route('materias.store') }}" class="formulario">
    @csrf

    <label for="clave" class="campo__etiqueta">Clave</label>
    <input id="clave" name="clave" type="text" maxlength="20" value="{{ old('clave') }}"
           class="campo @error('clave') campo--error @enderror" required autofocus>
    @error('clave') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="nombre" class="campo__etiqueta">Nombre</label>
    <input id="nombre" name="nombre" type="text" maxlength="100" value="{{ old('nombre') }}"
           class="campo @error('nombre') campo--error @enderror" required>
    @error('nombre') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="horas_semana" class="campo__etiqueta">Horas por semana</label>
    <input id="horas_semana" name="horas_semana" type="number" min="1" max="40" value="{{ old('horas_semana') }}"
           class="campo @error('horas_semana') campo--error @enderror" required>
    @error('horas_semana') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="clave_corta" class="campo__etiqueta">Clave corta</label>
    <input id="clave_corta" name="clave_corta" type="text" maxlength="10"
           value="{{ old('clave_corta', $materia->clave_corta ?? '') }}"
           class="campo @error('clave_corta') campo--error @enderror" required>
    @error('clave_corta') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="semestre" class="campo__etiqueta">Semestre</label>
    <select id="semestre" name="semestre"
            class="campo @error('semestre') campo--error @enderror" required>
        @foreach ([1 => '1.º', 2 => '2.º', 3 => '3.º', 4 => '4.º', 5 => '5.º', 6 => '6.º'] as $valor => $texto)
            <option value="{{ $valor }}" @selected((int) old('semestre', $materia->semestre ?? 1) === $valor)>{{ $texto }}</option>
        @endforeach
    </select>
    @error('semestre') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="tipo" class="campo__etiqueta">Tipo</label>
    <select id="tipo" name="tipo"
            class="campo @error('tipo') campo--error @enderror" required>
        @foreach (['normal' => 'Normal', 'bloque_comun' => 'Bloque común', 'tutoria' => 'Tutoría'] as $valor => $texto)
            <option value="{{ $valor }}" @selected(old('tipo', $materia->tipo ?? 'normal') === $valor)>{{ $texto }}</option>
        @endforeach
    </select>
    @error('tipo') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="tipo" class="campo__etiqueta">Tipo</label>
    <select id="tipo" name="tipo"
            class="campo @error('tipo') campo--error @enderror" required>
        @foreach (['salon' => 'Salón', 'laboratorio' => 'Laboratorio', 'auditorio' => 'Auditorio'] as $valor => $texto)
            <option value="{{ $valor }}" @selected(old('tipo', $aula->tipo ?? 'salon') === $valor)>{{ $texto }}</option>
        @endforeach
    </select>
    @error('tipo') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
    
    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('materias.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection