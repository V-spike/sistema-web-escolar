@extends('layouts.app')

@section('titulo', 'Editar semestre')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Editar semestre</h1>

<form method="POST" action="{{ route('semestres.update', $semestre) }}" class="formulario">
    @csrf
    @method('PUT')

    <label for="nombre" class="campo__etiqueta">Nombre</label>
    <input id="nombre" name="nombre" type="text" maxlength="50" placeholder="Ej. Agosto–Diciembre 2026" value="{{ old('nombre', $semestre->nombre) }}"
           class="campo @error('nombre') campo--error @enderror" required autofocus>
    @error('nombre') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    {{-- El campo date solo entiende año-mes-día (Y-m-d) --}}
    <label for="fecha_inicio" class="campo__etiqueta">Fecha de inicio</label>
    <input id="fecha_inicio" name="fecha_inicio" type="date" value="{{ old('fecha_inicio', $semestre->fecha_inicio->format('Y-m-d')) }}"
           class="campo @error('fecha_inicio') campo--error @enderror" required>
    @error('fecha_inicio') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="fecha_fin" class="campo__etiqueta">Fecha de fin</label>
    <input id="fecha_fin" name="fecha_fin" type="date" value="{{ old('fecha_fin', $semestre->fecha_fin->format('Y-m-d')) }}"
           class="campo @error('fecha_fin') campo--error @enderror" required>
    @error('fecha_fin') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('semestres.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection
