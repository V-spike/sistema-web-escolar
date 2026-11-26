{{-- Usa la plantilla de invitado --}}
@extends('layouts.invitado')
{{-- Rellena el apartado de la plantilla con el título --}}
@section('titulo', 'Iniciar sesión')

@section('contenido')
    <div class="tarjeta tarjeta--login">
        @include('componentes.marca')
        <h1>Bienvenido de nuevo</h1>
        <p class="tenue">Inicia sesión con tu cuenta institucional.</p>

        {{-- Aviso de otra pantalla, por ejemplo al cambiar la contraseña --}}
        @if (session('estado'))
            <div class="mensaje mensaje--exito">{{ session('estado') }}</div>
        @endif

        {{-- Manda los datos a AccesoController@entrar --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label class="campo__etiqueta" for="correo">Correo electrónico</label>
            <input class="campo @error('email') campo--error @enderror" type="email" id="correo" name="email" value="{{ old('email') }}" required autofocus>

            {{-- Error de correo o contraseña, si lo hay --}}
            @error('email')
                <div class="mensaje mensaje--error">{{ $message }}</div>
            @enderror

            <label class="campo__etiqueta" for="clave">Contraseña</label>
            <input class="campo" type="password" id="clave" name="password" required>

            <button class="btn btn--primario" type="submit" style="margin-top:1.25rem">Entrar</button>
        </form>

        <a class="enlace" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
    </div>
    <p class="pie-escuela">{{ config('escolar.escuela.direccion_general') }} · Clave {{ config('escolar.escuela.clave') }} · {{ config('escolar.escuela.localidad') }}</p>
@endsection
