@extends('layouts.app')

@section('titulo', 'Panel del superusuario')

@section('menu')
    <a href="#" class="menu__opcion menu__opcion--activa">Inicio</a>
    <span class="menu__opcion menu__opcion--pendiente">Administradores</span>
    <span class="menu__opcion menu__opcion--pendiente">Materias</span>
    <span class="menu__opcion menu__opcion--pendiente">Aulas</span>
    <span class="menu__opcion menu__opcion--pendiente">Semestres</span>
    <span class="menu__opcion menu__opcion--pendiente">Grupos</span>
    <span class="menu__opcion menu__opcion--pendiente">Alumnos</span>
    <span class="menu__opcion menu__opcion--pendiente">Docentes</span>
    <span class="menu__opcion menu__opcion--pendiente">Cambiar contraseña</span>
@endsection

@section('contenido')
    <h1>Bienvenido, {{ auth()->user()->name }}</h1>
    <p>Panel provisional del superusuario. Las opciones del menú se activan en los siguientes Sprints.</p>
@endsection
