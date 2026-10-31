@extends('layouts.app')

@section('titulo', 'Panel del alumno')

@section('menu')
    <a href="#" class="menu__opcion menu__opcion--activa">Inicio</a>
    <span class="menu__opcion menu__opcion--pendiente">Mi horario</span>
    <span class="menu__opcion menu__opcion--pendiente">Mis calificaciones</span>
    <span class="menu__opcion menu__opcion--pendiente">Cambiar contraseña</span>
@endsection

@section('contenido')
    <h1>Bienvenido, {{ auth()->user()->name }}</h1>
    <p>Panel provisional del alumno. Aquí podrás ver tu horario y tus calificaciones en los siguientes Sprints.</p>
@endsection
