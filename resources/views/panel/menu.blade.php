{{-- Menú compartido de los paneles, catálogos y "Cambiar contraseña".
     Muestra las opciones según el rol y resalta la sección en la que está el usuario. --}}
@php
    $rol = auth()->user()->rol;

    // [texto, nombre de ruta, patrón para resaltar]
    $catalogos = [
        ['Materias', 'materias.index', 'materias.*'],
        ['Aulas', 'aulas.index', 'aulas.*'],
        ['Semestres', 'semestres.index', 'semestres.*'],
        ['Jornada', 'jornada.index', 'jornada.*'],
        ['Bloques comunes', 'bloques-comunes.index', 'bloques-comunes.*'],
        ['Docentes', 'docentes.index', 'docentes.*'],
        ['Grupos', 'grupos.index', 'grupos.*'],
    ];

    $listas = [
        'superusuario'  => array_merge([['Administradores', 'administradores.index', 'administradores.*']], $catalogos),
        'administrador' => $catalogos, // el administrador no ve "Administradores" (HU-03, criterio 4)
    ][$rol] ?? [];

    $pendientes = [
        'superusuario'  => ['Alumnos'],
        'administrador' => ['Alumnos'],
        'docente'       => ['Mi horario', 'Calificaciones'],
        'alumno'        => ['Mi horario', 'Mis calificaciones'],
    ][$rol] ?? [];
@endphp

<a href="{{ route('panel.' . $rol) }}"
   @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs('panel.*')])>Inicio</a>

@foreach ($listas as [$texto, $ruta, $patron])
    {{-- Si la ruta todavía no está en main, la opción se ve en gris en lugar de romper la página --}}
    @if (\Illuminate\Support\Facades\Route::has($ruta))
        <a href="{{ route($ruta) }}"
           @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs($patron)])>{{ $texto }}</a>
    @else
        <span class="menu__opcion menu__opcion--pendiente">{{ $texto }}</span>
    @endif
@endforeach

@foreach ($pendientes as $opcion)
    <span class="menu__opcion menu__opcion--pendiente">{{ $opcion }}</span>
@endforeach

<a href="{{ route('contrasena.editar') }}"
   @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs('contrasena.editar')])>Cambiar contraseña</a>
