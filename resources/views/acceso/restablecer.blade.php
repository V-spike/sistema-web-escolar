{{-- Usa la plantilla de invitado --}}
@extends('layouts.invitado')
{{-- Rellena el apartado de la plantilla con el título --}}
@section('titulo', 'Crear contraseña nueva')

{{-- Rellenar los demás apartados --}}
@section('contenido')
    <h1>Crear contraseña nueva</h1>

    {{-- Apunta a una ruta con su nombre --}}
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="email" value="{{ old('email', $email) }}" required>
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Mensaje de error de ese campo, si lo hay --}}
        @error('email')
            <div class="mensaje mensaje--error">{{ $message }}</div>
        @enderror
        <label for="clave">Contraseña nueva</label>

        <input type="password" id="clave" name="password" required>

        <label for="clave2">Confirmar contraseña</label>

        <input type="password" id="clave2" name="password_confirmation" required>

        @error('password')
            <div class="mensaje mensaje--error">{{ $message }}</div>
        @enderror

        <button type="submit">Guardar contraseña</button>
    </form>
@endsection
