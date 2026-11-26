@extends('layouts.app')

@section('titulo', 'Inicio')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
    @include('panel._resumen')
@endsection
