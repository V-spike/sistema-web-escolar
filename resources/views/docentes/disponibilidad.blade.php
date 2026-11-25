@extends('layouts.app')

@php
    // Mientras se confirman los nombres de la tabla docentes (T-44 de Marcos)
    $nombreDocente = $docente->nombre ?? $docente->user?->name ?? 'docente';
    $horasPlaza = $docente->horas_plaza ?? null;
@endphp

@section('titulo', 'Disponibilidad de ' . $nombreDocente)

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Disponibilidad de {{ $nombreDocente }}</h1>

<p>
    Marca los espacios en los que el docente puede dar clase.
    @if ($horasPlaza)
        Sus horas de plaza son <strong>{{ $horasPlaza }}</strong> a la semana.
    @endif
</p>

@error('espacios.*') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

<form method="POST" action="{{ route('docentes.disponibilidad.guardar', $docente) }}">
    @csrf
    @method('PUT')

    @include('componentes.cuadricula', [
        'franjas' => $franjas,
        'celda'   => 'docentes._celda-disponibilidad',
        'datos'   => ['marcadas' => $marcadas],
    ])

    <p class="disponibilidad__contador" aria-live="polite">
        Marcados: <strong id="total-marcados">0</strong> espacios
    </p>

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ \Illuminate\Support\Facades\Route::has('docentes.index') ? route('docentes.index') : url()->previous() }}"
           class="btn btn--secundario btn--enlace">Regresar</a>
    </div>
</form>

<style>
    /* La casilla ocupa toda la celda para que sea fácil de presionar */
    .disponibilidad__casilla { display: flex; align-items: center; justify-content: center; width: 100%; min-height: 36px; cursor: pointer; }
    .disponibilidad__casilla input { width: 1.1rem; height: 1.1rem; cursor: pointer; }
    /* :has() pinta la celda cuando su casilla está marcada, sin JavaScript.
       Mismo color que .cuadricula__celda--marcada de la guía de estilo. */
    .cuadricula__celda:has(input:checked) { background: var(--color-primario-claro); }
    .disponibilidad__contador { margin: 1rem 0 0; }
</style>

<script>
    // Cuenta las casillas marcadas y actualiza el texto "Marcados: N espacios"
    const casillas = document.querySelectorAll('input[name="espacios[]"]');
    const total = document.getElementById('total-marcados');

    function contar() {
        total.textContent = [...casillas].filter(c => c.checked).length;
    }

    // Cada vez que se marca o desmarca una casilla
    casillas.forEach(c => c.addEventListener('change', contar));

    // Una vez al cargar, para contar las que ya venían marcadas de la base de datos
    contar();
</script>
@endsection
