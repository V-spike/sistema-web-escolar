{{-- Plantilla común de las páginas de error. Cada error solo cambia el código y el texto. --}}
@extends('layouts.invitado')

@section('contenido')
    <div class="tarjeta tarjeta--login error-pagina">
        <p class="error-pagina__codigo">@yield('codigo')</p>
        <h1>@yield('titulo')</h1>
        <p class="tenue">@yield('mensaje')</p>

        @auth
            <a class="btn btn--primario" href="{{ route('panel.' . auth()->user()->rol) }}">Volver a mi inicio</a>
        @else
            <a class="btn btn--primario" href="{{ route('login') }}">Ir al inicio de sesión</a>
        @endauth
    </div>
@endsection
