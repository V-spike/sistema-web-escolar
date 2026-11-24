@extends('layouts.app')

@section('titulo', 'Editar franja')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Editar franja {{ $franja->orden }}</h1>

<form method="POST" action="{{ route('jornada.update', $franja) }}" class="formulario">
    @csrf
    @method('PUT')

    {{-- El campo time solo entiende horas:minutos (H:i). En la base viene "07:00:00", por eso substr(..., 0, 5) --}}
    <label for="hora_inicio" class="campo__etiqueta">Hora de inicio</label>
    <input id="hora_inicio" name="hora_inicio" type="time"
           value="{{ old('hora_inicio', substr($franja->hora_inicio, 0, 5)) }}"
           class="campo @error('hora_inicio') campo--error @enderror" required autofocus>
    @error('hora_inicio') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="hora_fin" class="campo__etiqueta">Hora de fin</label>
    <input id="hora_fin" name="hora_fin" type="time"
           value="{{ old('hora_fin', substr($franja->hora_fin, 0, 5)) }}"
           class="campo @error('hora_fin') campo--error @enderror" required>
    @error('hora_fin') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    {{-- Si hubo un error, se respeta lo que el usuario marcó; si no, lo que hay en la base --}}
    <label class="campo__etiqueta">
        <input type="checkbox" name="es_receso" value="1"
               @checked($errors->any() ? old('es_receso') : $franja->es_receso)>
        Es receso
    </label>
    <p class="campo__ayuda">En un receso no se asignan clases ni se marca disponibilidad.</p>

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('jornada.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection
