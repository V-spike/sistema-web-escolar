@extends('layouts.app')

@section('titulo', 'Panel del alumno')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
    <h1>Bienvenido, {{ auth()->user()->name }}</h1>
    <p>Panel provisional del alumno. Aquí podrás ver tu horario y tus calificaciones en los siguientes Sprints.</p>
@endsection
