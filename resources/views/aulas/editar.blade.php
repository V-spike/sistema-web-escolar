@extends('layouts.app')

@section('titulo', 'Editar aula')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Editar aula</h1>

<form method="POST" action="{{ route('aulas.update', $aula) }}" class="formulario">
    @csrf
    @method('PUT')

    <label for="clave" class="campo__etiqueta">Clave</label>
    <input id="clave" name="clave" type="text" maxlength="20" value="{{ old('clave', $aula->clave) }}"
           class="campo @error('clave') campo--error @enderror" required autofocus>
    @error('clave') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="edificio" class="campo__etiqueta">Edificio</label>
    <input id="edificio" name="edificio" type="text" maxlength="50" value="{{ old('edificio', $aula->edificio) }}"
           class="campo @error('edificio') campo--error @enderror" required>
    @error('edificio') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="capacidad" class="campo__etiqueta">Capacidad</label>
    <input id="capacidad" name="capacidad" type="number" min="1" max="200" value="{{ old('capacidad', $aula->capacidad) }}"
           class="campo @error('capacidad') campo--error @enderror" required>
    @error('capacidad') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('aulas.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection