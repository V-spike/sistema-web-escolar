<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Sistema web escolar')</title>
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
</head>
<body>
    <header class="encabezado">
        <span class="encabezado__titulo">Sistema web escolar</span>
        <div class="encabezado__usuario">
            <span>{{ auth()->user()->name }} · {{ auth()->user()->rol }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn--secundario">Cerrar sesión</button>
            </form>
        </div>
    </header>
    <div class="distribucion">
        <nav class="menu">@yield('menu')</nav>
        <main class="contenido">
            @if (session('estado'))
                <div class="mensaje mensaje--exito">{{ session('estado') }}</div>
            @endif
            @yield('contenido')
        </main>
    </div>
</body>
</html>