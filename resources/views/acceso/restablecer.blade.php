{{-- Usa la plantilla de invitado --}}
@extends('layouts.invitado')
{{-- Rellena el apartado de la plantilla con el título --}}
@section('titulo', 'Crear contraseña nueva')

{{-- Rellenar los demás apartados --}}
@section('contenido')
<div class="tarjeta tarjeta--login">
    <div class="marca"><span class="marca__logo">SE</span> Sistema escolar</div>
    <h1>Crear contraseña nueva</h1>

    {{-- Apunta a una ruta con su nombre --}}
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <label class="campo__etiqueta" for="correo">Correo electrónico</label>
        <input class="campo" type="email" id="correo" name="email" value="{{ old('email', $email) }}" required>
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Mensaje de error de ese campo, si lo hay --}}
        @error('email')
            <div class="mensaje mensaje--error">{{ $message }}</div>
        @enderror
        <label class="campo__etiqueta" for="clave">Contraseña nueva</label>

        <input class="campo" type="password" id="clave" name="password" required>

        <label class="campo__etiqueta" for="clave2">Confirmar contraseña</label>

        <input class="campo" type="password" id="clave2" name="password_confirmation" required>

        @error('password')
            <div class="mensaje mensaje--error">{{ $message }}</div>
        @enderror

        <button class="btn btn--primario" type="submit" style="margin-top:1.25rem">Guardar contraseña</button>
    </form>
    <a class="enlace" href="{{ route('login') }}">Volver a iniciar sesión</a>
</div>
@endsection
