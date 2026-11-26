@extends('layouts.app')

@section('titulo', 'Mi horario')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo">
    <div>
        <h1>Mi horario</h1>
        <p class="tenue" style="margin:0">{{ auth()->user()->name }}{{ $grupo ? ' · grupo ' . $grupo->nombre : '' }}{{ $semestre ? ' · ' . $semestre->nombre : '' }}</p>
    </div>
    <button type="button" class="btn btn--secundario btn--chico no-imprimir" onclick="window.print()">Imprimir</button>
</div>

@if (! $grupo)
    <div class="mensaje mensaje--aviso">Todavía no tienes grupo asignado. Pídeselo a la administración escolar.</div>
@elseif ($clases->isEmpty())
    <div class="mensaje mensaje--aviso">Tu grupo todavía no tiene clases en el horario.</div>
@endif

<div class="panel">
    @include('componentes.semana', ['semana' => $semana])
</div>
@endsection
