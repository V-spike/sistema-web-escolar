@extends('layouts.app')

@section('titulo', 'Editar docente')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Editar docente</h1>

<form method="POST" action="{{ route('docentes.update', $docente) }}" class="formulario">
    @csrf
    @method('PUT')

    {{-- 1. Datos personales --}}
    <fieldset class="formulario__grupo">
        <legend>Datos personales</legend>

        <label for="nombre" class="campo__etiqueta">Nombre(s)</label>
        <input id="nombre" name="nombre" type="text" maxlength="60" value="{{ old('nombre', $docente->nombre ?? '') }}"
               class="campo @error('nombre') campo--error @enderror" required autofocus>
        @error('nombre') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="apellido_paterno" class="campo__etiqueta">Apellido paterno</label>
        <input id="apellido_paterno" name="apellido_paterno" type="text" maxlength="60" value="{{ old('apellido_paterno', $docente->apellido_paterno ?? '') }}"
               class="campo @error('apellido_paterno') campo--error @enderror" required>
        @error('apellido_paterno') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="apellido_materno" class="campo__etiqueta">Apellido materno</label>
        <input id="apellido_materno" name="apellido_materno" type="text" maxlength="60" value="{{ old('apellido_materno', $docente->apellido_materno ?? '') }}"
               class="campo @error('apellido_materno') campo--error @enderror">
        @error('apellido_materno') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="curp" class="campo__etiqueta">CURP</label>
        <input id="curp" name="curp" type="text" minlength="18" maxlength="18" value="{{ old('curp', $docente->curp ?? '') }}"
               class="campo @error('curp') campo--error @enderror" style="text-transform: uppercase">
        @error('curp') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="rfc" class="campo__etiqueta">RFC</label>
        <input id="rfc" name="rfc" type="text" maxlength="13" value="{{ old('rfc', $docente->rfc ?? '') }}"
               class="campo @error('rfc') campo--error @enderror" style="text-transform: uppercase">
        @error('rfc') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="telefono" class="campo__etiqueta">Teléfono</label>
        <input id="telefono" name="telefono" type="tel" maxlength="20" value="{{ old('telefono', $docente->telefono ?? '') }}"
               class="campo @error('telefono') campo--error @enderror">
        @error('telefono') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="correo" class="campo__etiqueta">Correo</label>
        <input id="correo" name="correo" type="email" maxlength="100" value="{{ old('correo', $docente->correo ?? '') }}"
               class="campo @error('correo') campo--error @enderror">
        @error('correo') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
    </fieldset>

    {{-- 2. Plaza --}}
    <fieldset class="formulario__grupo">
        <legend>Plaza</legend>

        <label for="numero_personal" class="campo__etiqueta">Número de personal</label>
        <input id="numero_personal" name="numero_personal" type="text" maxlength="20" value="{{ old('numero_personal', $docente->numero_personal ?? '') }}"
               class="campo @error('numero_personal') campo--error @enderror" required>
        @error('numero_personal') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="categoria" class="campo__etiqueta">Categoría</label>
        <input id="categoria" name="categoria" type="text" maxlength="50" value="{{ old('categoria', $docente->categoria ?? '') }}"
               class="campo @error('categoria') campo--error @enderror">
        @error('categoria') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="sindicato" class="campo__etiqueta">Sindicato</label>
        <input id="sindicato" name="sindicato" type="text" maxlength="30" value="{{ old('sindicato', $docente->sindicato ?? '') }}"
               class="campo @error('sindicato') campo--error @enderror">
        @error('sindicato') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="titulo" class="campo__etiqueta">Título</label>
        <input id="titulo" name="titulo" type="text" maxlength="100" value="{{ old('titulo', $docente->titulo ?? '') }}"
               class="campo @error('titulo') campo--error @enderror">
        @error('titulo') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="cedula" class="campo__etiqueta">Cédula profesional</label>
        <input id="cedula" name="cedula" type="text" maxlength="20" value="{{ old('cedula', $docente->cedula ?? '') }}"
               class="campo @error('cedula') campo--error @enderror">
        @error('cedula') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

        <label for="horas_plaza" class="campo__etiqueta">Horas de plaza</label>
        <input id="horas_plaza" name="horas_plaza" type="number" min="1" max="40" value="{{ old('horas_plaza', $docente->horas_plaza ?? '') }}"
               class="campo @error('horas_plaza') campo--error @enderror" required>
        @error('horas_plaza') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
    </fieldset>

    {{-- 3. Perfil: materias que puede impartir --}}
    @php
        $elegidas = old('materias', isset($docente) ? $docente->materias->pluck('id')->all() : []);
    @endphp
    <fieldset class="formulario__grupo">
        <legend>Materias que puede impartir</legend>

        @forelse ($materias->groupBy('semestre') as $semestre => $delSemestre)
            <h3 class="formulario__subtitulo">{{ $semestre }}.º semestre</h3>
            <div class="casillas">
                @foreach ($delSemestre as $materia)
                    <label class="casilla">
                        <input type="checkbox" name="materias[]" value="{{ $materia->id }}"
                               @checked(in_array($materia->id, $elegidas))>
                        {{ $materia->clave_corta }} · {{ $materia->nombre }}
                    </label>
                @endforeach
            </div>
        @empty
            <p>No hay materias activas registradas.</p>
        @endforelse

        @error('materias') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
        @error('materias.*') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror
    </fieldset>
    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('docentes.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection