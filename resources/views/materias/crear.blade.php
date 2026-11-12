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

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('materias.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection