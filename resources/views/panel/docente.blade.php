@extends('layouts.app')

@section('titulo', 'Panel del docente')

@section('menu')
    <a href="#" class="menu__opcion menu__opcion--activa">Inicio</a>
    <span class="menu__opcion menu__opcion--pendiente">Mi horario</span>
    <span class="menu__opcion menu__opcion--pendiente">Calificaciones</span>
    <span class="menu__opcion menu__opcion--pendiente">Cambiar contraseña</span>
@endsection

@section('contenido')
    <h1>Bienvenido, {{ auth()->user()->name }}</h1>
    <p>Panel provisional del docente. Aquí podrás ver tu horario y capturar calificaciones en los siguientes Sprints.</p>
@endsection
