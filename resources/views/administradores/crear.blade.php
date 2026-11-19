@extends('layouts.app')

@section('titulo', 'Nuevo administrador')

@section('menu')
    @include('panel.menu')
@endsection

@section('contenido')
<h1>Nuevo administrador</h1>

<form method="POST" action="{{ route('administradores.store') }}" class="formulario">
    @csrf

    <label for="name" class="campo__etiqueta">Nombre</label>
    <input id="name" name="name" type="text" maxlength="255" value="{{ old('name') }}"
           class="campo @error('name') campo--error @enderror" required autofocus>
    @error('name') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="email" class="campo__etiqueta">Correo electrónico</label>
    <input id="email" name="email" type="email" maxlength="255" value="{{ old('email') }}"
           class="campo @error('email') campo--error @enderror" required>
    @error('email') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    {{-- Los campos de contraseña nunca llevan old(): no se devuelve una contraseña al navegador --}}
    <label for="password" class="campo__etiqueta">Contraseña</label>
    <input id="password" name="password" type="password" autocomplete="new-password"
           class="campo @error('password') campo--error @enderror" required>
    @error('password') <div class="mensaje mensaje--error">{{ $message }}</div> @enderror

    <label for="password_confirmation" class="campo__etiqueta">Confirma la contraseña</label>
    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
           class="campo" required>

    <div class="formulario__acciones">
        <button type="submit" class="btn btn--primario">Guardar</button>
        <a href="{{ route('administradores.index') }}" class="btn btn--secundario btn--enlace">Cancelar</a>
    </div>
</form>
@endsection
