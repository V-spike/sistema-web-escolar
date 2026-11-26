@extends('layouts.app')

@section('titulo', 'Cambiar contraseña')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
    <h1>Actualizar contraseña</h1>

    <form method="POST" action="{{ route('contrasena.actualizar') }}" class="formulario">
        @csrf
        <label class="campo__etiqueta" for="password_actual">Contraseña actual</label>
        <input class="campo" type="password" id="password_actual" name="password_actual" required>

        {{-- Mensaje de error de ese campo, si lo hay --}}
        @error('password_actual')
            <div class="mensaje mensaje--error">{{ $message }}</div>
        @enderror

        <label class="campo__etiqueta" for="password">Contraseña nueva</label>
        <input class="campo" type="password" id="password" name="password" required>

        {{-- Mensaje de error de ese campo, si lo hay --}}
        @error('password')
            <div class="mensaje mensaje--error">{{ $message }}</div>
        @enderror
        <label class="campo__etiqueta" for="password_confirmation">Confirma tu nueva contraseña</label>
        <input class="campo" type="password" id="password_confirmation" name="password_confirmation" required>

        <button class="btn btn--primario" type="submit" style="margin-top:1.25rem">Cambiar contraseña</button>
    </form>
@endsection
