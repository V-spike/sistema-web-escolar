{{-- Menú lateral: las opciones dependen del rol y se resalta la sección actual. --}}
@php
    $rol = auth()->user()->rol;

    // [texto, nombre de ruta, patrón para resaltar]
    $gestion = [
        'Horario' => [
            ['Acomodar horario', 'horarios.index', 'horarios.*'],
        ],
        'Calificaciones' => [
            ['Calificaciones y boletas', 'calificaciones.index', ['calificaciones.*', 'boletas.*']],
        ],
        'Escuela' => [
            ['Semestres', 'semestres.index', 'semestres.*'],
            ['Jornada y recesos', 'jornada.index', 'jornada.*'],
            ['Materias', 'materias.index', 'materias.*'],
            ['Aulas', 'aulas.index', 'aulas.*'],
        ],
        'Personas' => [
            ['Docentes', 'docentes.index', 'docentes.*'],
            ['Grupos', 'grupos.index', 'grupos.*'],
            ['Alumnos', 'alumnos.index', 'alumnos.*'],
        ],
    ];

    $secciones = match ($rol) {
        'superusuario' => array_merge_recursive($gestion, ['Personas' => [['Administradores', 'administradores.index', 'administradores.*']]]),
        'administrador' => $gestion,
        'docente' => ['Escuela' => [['Calificaciones', 'calificaciones.index', 'calificaciones.*']]],
        'alumno' => ['Escuela' => [['Mi boleta', 'boletas.mia', 'boletas.*']]],
        default => [],
    };

    $inicio = match ($rol) {
        'docente', 'alumno' => 'Mi horario',
        default => 'Inicio',
    };
@endphp

<a href="{{ route('panel.' . $rol) }}"
   @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs('panel.*')])>{{ $inicio }}</a>

@foreach ($secciones as $titulo => $opciones)
    <span class="menu__seccion">{{ $titulo }}</span>
    @foreach ($opciones as [$texto, $ruta, $patron])
        <a href="{{ route($ruta) }}"
           @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs($patron)])>{{ $texto }}</a>
    @endforeach
@endforeach

<span class="menu__seccion">Cuenta</span>
<a href="{{ route('contrasena.editar') }}"
   @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs('contrasena.editar')])>Cambiar contraseña</a>
