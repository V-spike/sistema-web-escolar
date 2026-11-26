{{-- Usa la plantilla de invitado --}}
@extends('layouts.invitado')
{{-- Rellena el apartado de la plantilla con el título --}}
@section('titulo', 'Recuperar contraseña')

{{-- Rellenar los demás apartados --}}
@section('contenido')
<div class="tarjeta tarjeta--login">
    <div class="marca"><span class="marca__logo">SE</span> Sistema escolar</div>
    <h1>Recuperar contraseña</h1>
    
    {{-- Aviso del controlador --}}
    @if (session('estado'))
        <div class="mensaje mensaje--exito">{{ session('estado') }}</div>
    @endif

    {{-- Apunta a una ruta con su nombre --}}
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label class="campo__etiqueta" for="correo">Correo electrónico</label>
        <input class="campo" type="email" id="correo" name="email" value="{{ old('email') }}" required>

        {{-- Mensaje de error de ese campo, si lo hay --}}
        @error('email')
            <div class="mensaje mensaje--error">{{ $message }}</div>
        @enderror

        <button class="btn btn--primario" type="submit" style="margin-top:1.25rem">Enviar enlace</button>
    </form>
    <a class="enlace" href="{{ route('login') }}">Volver a iniciar sesión</a>
</div>
@endsection
