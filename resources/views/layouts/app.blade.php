<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Sistema web escolar') · Sistema escolar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v=8">
</head>
<body>
    <div class="distribucion">
        <aside class="lateral">
            @include('componentes.marca')
            <nav class="menu">@yield('menu')</nav>
        </aside>

        <div class="principal">
            <header class="encabezado">
                <span class="encabezado__titulo">@yield('titulo', 'Sistema web escolar')</span>
                <div class="encabezado__usuario">
                    <span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span>{{ auth()->user()->name }}<span class="rol">{{ auth()->user()->rol }}</span></span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn--secundario btn--chico">Cerrar sesión</button>
                    </form>
                </div>
            </header>

            <main class="contenido">
                @if (session('estado'))
                    <div class="mensaje mensaje--exito" role="status">{{ session('estado') }}</div>
                @endif
                @foreach (['estado', 'rango', 'semestre'] as $campo)
                    @error($campo) <div class="mensaje mensaje--error" role="alert">{{ $message }}</div> @enderror
                @endforeach
                @yield('contenido')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
