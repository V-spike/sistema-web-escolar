{{-- Usa la plantilla de invitado --}}
@extends('layouts.invitado')
{{-- Rellena el apartado de la plantilla con el título --}}
@section('titulo', 'Recuperar contraseña')

{{-- Rellenar los demás apartados --}}
@section('contenido')
    <h1>Recuperar contraseña</h1>
    
    {{-- Aviso del controlador --}}
    @if (session('estado'))
        <p>{{ session('estado') }}</p>
    @endif

    {{-- Apunta a una ruta con su nombre --}}
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="email" value="{{ old('email') }}" required>

        {{-- Mensaje de error de ese campo, si lo hay --}}
        @error('email')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">Enviar enlace</button>
    </form>
@endsection