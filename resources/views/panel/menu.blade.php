{{-- Menú lateral: las opciones dependen del rol y se resalta la sección actual. --}}
@php
    $rol = auth()->user()->rol;

    // Cada opción: [texto, nombre de ruta, patrón para resaltar, ícono]
    $gestion = [
        'Horario y calificaciones' => [
            ['Armar horario', 'horarios.index', 'horarios.*', 'horario'],
            ['Calificaciones', 'calificaciones.index', ['calificaciones.*', 'boletas.*'], 'calificaciones'],
        ],
        'Catálogos de la escuela' => [
            ['Semestres', 'semestres.index', 'semestres.*', 'semestres'],
            ['Jornada y recesos', 'jornada.index', 'jornada.*', 'jornada'],
            ['Materias', 'materias.index', 'materias.*', 'materias'],
            ['Aulas', 'aulas.index', 'aulas.*', 'aulas'],
        ],
        'Personas' => [
            ['Docentes', 'docentes.index', 'docentes.*', 'docentes'],
            ['Grupos', 'grupos.index', 'grupos.*', 'grupos'],
            ['Alumnos', 'alumnos.index', 'alumnos.*', 'alumnos'],
        ],
    ];

    $secciones = match ($rol) {
        'superusuario' => array_merge_recursive($gestion, ['Personas' => [['Administradores', 'administradores.index', 'administradores.*', 'administradores']]]),
        'administrador' => $gestion,
        'docente' => ['Mi trabajo' => [['Calificaciones', 'calificaciones.index', ['calificaciones.*', 'boletas.*'], 'calificaciones']]],
        'alumno' => ['Mi escuela' => [['Mis calificaciones', 'calificaciones.mias', 'calificaciones.mias', 'calificaciones']]],
        default => [],
    };

    // La primera opción: "Inicio" para la administración, "Mi horario" para docentes y alumnos
    [$inicio, $iconoInicio] = in_array($rol, ['docente', 'alumno'], true) ? ['Mi horario', 'horario'] : ['Inicio', 'inicio'];
@endphp

<a href="{{ route('panel.' . $rol) }}"
   @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs('panel.*')])>
    @include('componentes.icono', ['nombre' => $iconoInicio]) {{ $inicio }}
</a>

@foreach ($secciones as $titulo => $opciones)
    <div class="menu__grupo">
        <span class="menu__seccion">{{ $titulo }}</span>
        @foreach ($opciones as [$texto, $ruta, $patron, $icono])
            <a href="{{ route($ruta) }}"
               @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs($patron)])>
                @include('componentes.icono', ['nombre' => $icono]) {{ $texto }}
            </a>
        @endforeach
    </div>
@endforeach

<div class="menu__grupo">
    <span class="menu__seccion">Cuenta</span>
    <a href="{{ route('contrasena.editar') }}"
       @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs('contrasena.editar')])>
        @include('componentes.icono', ['nombre' => 'contrasena']) Cambiar contraseña
    </a>
</div>
