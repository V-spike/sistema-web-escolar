@extends('layouts.app')

@section('titulo', 'Cambiar contraseña')

@section('menu')
    <a href= "{{ route('panel.' . auth()->user()->rol) }}" class= "menu__opcion">Volver a mi panel</a>

@endsection

@section('contenido')
    <h1>Actualizar contraseña</h1>

    <form method="POST" action="{{ route('contrasena.actualizar') }}">
        @csrf
        <label for="password_actual">Contraseña actual</label>
        <input type="password" id="password_actual" name="password_actual" required>

        {{-- Mensaje de error de ese campo, si lo hay --}}
        @error('password_actual')
            <p>{{ $message }}</p>
        @enderror
        <label for="password">Contraseña nueva</label>
        <input type="password" id="password" name="password" required>

        {{-- Mensaje de error de ese campo, si lo hay --}}
        @error('password')
            <p>{{ $message }}</p>
        @enderror
        <label for="password_confirmation">Confirma tu nueva contraseña</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>

        <button type="submit">Cambiar contraseña</button>
    </form>
@endsection
