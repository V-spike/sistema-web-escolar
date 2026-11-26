@extends('layouts.app')

@section('titulo', 'Editar grupo')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Editar grupo {{ $grupo->grado }}.º {{ $grupo->letra }}</h1>

<form method="POST" action="{{ route('grupos.update', $grupo) }}" class="formulario">
    @csrf
    @method('PUT')

    {{-- ===== INICIO DEL BLOQUE DE CAMPOS (se reutiliza en editar) ===== --}}

    {{-- Periodo (semestre) --}}
    @php
        $semestreElegido = old('semestre_id', $grupo->semestre_id ?? ($semestreActivo->id ?? null));
    @endphp
    <label for="semestre_id" class="campo__etiqueta">Periodo</label>
    <select id="semestre_id" name="semestre_id"
            class="campo @error('semestre_id') campo--error @enderror" required>
        @foreach ($semestres as $semestre)
            <option value="{{ $semestre->id }}" @selected((int) $semestreElegido === $semestre->id)>
                {{ $semestre->nombre }}{{ $semestre->activo ? ' (activo)' : '' }}
            </option>
        @endforeach
    </select>
    @error('semestre_id') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    {{-- Grado --}}
    <label for="grado" class="campo__etiqueta">Grado</label>
    <select id="grado" name="grado"
            class="campo @error('grado') campo--error @enderror" required>
        @foreach ([1 => '1.º', 2 => '2.º', 3 => '3.º', 4 => '4.º', 5 => '5.º', 6 => '6.º'] as $valor => $texto)
            <option value="{{ $valor }}" @selected((int) old('grado', $grupo->grado ?? 1) === $valor)>{{ $texto }}</option>
        @endforeach
    </select>
    @error('grado') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    {{-- Letra --}}
    <label for="letra" class="campo__etiqueta">Letra</label>
    <input id="letra" name="letra" type="text" maxlength="1" pattern="[A-Za-z]"
           value="{{ old('letra', $grupo->letra ?? '') }}"
           class="campo @error('letra') campo--error @enderror"
           style="text-transform: uppercase; max-width: 5rem" required>
    @error('letra') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    {{-- Salón (opcional) --}}
    @php
        $aulaElegida = (string) old('aula_id', $grupo->aula_id ?? '');
    @endphp
    <label for="aula_id" class="campo__etiqueta">Salón</label>
    <select id="aula_id" name="aula_id"
            class="campo @error('aula_id') campo--error @enderror">
        <option value="" @selected($aulaElegida === '')>Sin salón</option>
        @foreach ($aulas as $aula)
            <option value="{{ $aula->id }}" @selected($aulaElegida === (string) $aula->id)>
                {{ $aula->clave }} · {{ $aula->edificio }}
            </option>
        @endforeach
    </select>
    @error('aula_id') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    {{-- Capacidad --}}
    <label for="capacidad" class="campo__etiqueta">Capacidad</label>
    <input id="capacidad" name="capacidad" type="number" min="1" max="50"
           value="{{ old('capacidad', $grupo->capacidad ?? 50) }}"
           class="campo @error('capacidad') campo--error @enderror" required>
    @error('capacidad') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    {{-- Área (solo 5.º y 6.º) --}}
    <label for="area" class="campo__etiqueta">Área</label>
    <input id="area" name="area" type="text" maxlength="60"
           value="{{ old('area', $grupo->area ?? '') }}"
           class="campo @error('area') campo--error @enderror">
    <small class="campo__ayuda">Solo 5.º y 6.º. Déjalo vacío en los demás grados.</small>
    @error('area') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    {{-- ===== FIN DEL BLOQUE DE CAMPOS ===== --}}

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('grupos.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection