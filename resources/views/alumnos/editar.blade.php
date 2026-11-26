@extends('layouts.app')

@section('titulo', 'Editar alumno')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Editar alumno</h1>

<form method="POST" action="{{ route('alumnos.update', $alumno) }}" class="formulario">
    @csrf
    @method('PUT')
    @include('alumnos._campos', ['alumno' => $alumno])

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('alumnos.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection
