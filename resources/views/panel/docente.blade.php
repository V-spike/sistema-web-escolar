@extends('layouts.app')

@section('titulo', 'Panel del docente')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
    <h1>Bienvenido, {{ auth()->user()->name }}</h1>
    <p>Panel provisional del docente. Aquí podrás ver tu horario y capturar calificaciones en los siguientes Sprints.</p>
@endsection
