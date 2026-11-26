@extends('layouts.app')

@section('titulo', $titulo)

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<div class="barra-catalogo no-imprimir">
    <div>
        <h1>{{ $titulo }}</h1>
        <p class="tenue" style="margin:0">Para exportarla usa "Imprimir / Guardar PDF" y elige "Guardar como PDF". Al imprimir sale una boleta por hoja.</p>
    </div>
    @if ($boletas->isNotEmpty())
        <button type="button" class="btn btn--primario btn--enlace" onclick="window.print()">Imprimir / Guardar PDF</button>
    @endif
</div>

@forelse ($boletas as $boleta)
    @include('boletas._boleta', ['boleta' => $boleta])
@empty
    <div class="mensaje mensaje--aviso">{{ $aviso }}</div>
@endforelse
@endsection
