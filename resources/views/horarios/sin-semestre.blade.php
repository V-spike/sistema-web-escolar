@extends('layouts.app')

@section('titulo', 'Acomodar horario')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Acomodar horario</h1>
<div class="panel">
    <p>Para acomodar el horario primero necesitas un semestre activo.</p>
    <a href="{{ route('semestres.index') }}" class="btn btn--primario btn--enlace">Ir a Semestres</a>
</div>
@endsection
