@extends('layouts.app')

@section('titulo', 'Nuevo alumno')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Nuevo alumno</h1>

<form method="POST" action="{{ route('alumnos.store') }}" class="formulario">
    @csrf

    @include('alumnos._campos')

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('alumnos.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection
