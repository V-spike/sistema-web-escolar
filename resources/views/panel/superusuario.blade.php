@extends('layouts.app')

@section('titulo', 'Panel del superusuario')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
    <h1>Bienvenido, {{ auth()->user()->name }}</h1>
    <p>Panel provisional del superusuario. Las opciones del menú se activan en los siguientes Sprints.</p>
@endsection
