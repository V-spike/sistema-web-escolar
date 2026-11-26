@extends('layouts.app')

@section('titulo', 'Mi horario')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <div>
        <h1>Mi horario</h1>
        <p class="tenue" style="margin:0">
            {{ $docente?->nombre_completo ?? auth()->user()->name }}{{ $semestre ? ' · ' . $semestre->nombre : '' }}
            @if ($clases->isNotEmpty()) · {{ \App\Horarios\Hora::duracion($clases->sum->minutos) }} a la semana @endif
        </p>
    </div>
    <button type="button" class="btn btn--secundario btn--chico no-imprimir" onclick="window.print()">Imprimir</button>
</div>

@if (! $docente)
    <div class="mensaje mensaje--aviso">Tu cuenta todavía no está ligada a un registro de docente. Pídeselo al administrador.</div>
@elseif ($clases->isEmpty())
    <div class="mensaje mensaje--aviso">Todavía no tienes clases asignadas en este semestre.</div>
@endif

<div class="panel">
    @include('componentes.semana', ['semana' => $semana])
</div>
@endsection
