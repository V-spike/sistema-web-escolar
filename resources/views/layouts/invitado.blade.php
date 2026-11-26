<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Sistema web escolar') · Sistema escolar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-escuela.png') }}">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v=17">
</head>
<body>
    <main class="contenedor-centrado">
        @yield('contenido')
    </main>
</body>
</html>
