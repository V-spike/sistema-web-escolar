{{-- Menú compartido de los 4 paneles y de "Cambiar contraseña".
     Muestra las opciones según el rol del usuario y resalta la página en la que está. --}}
@php
    $rol = auth()->user()->rol;

    // Opciones que todavía no existen (se ven en gris y no se pueden presionar)
    $pendientes = [
        'superusuario'  => ['Administradores', 'Materias', 'Aulas', 'Semestres', 'Grupos', 'Alumnos', 'Docentes'],
        'administrador' => ['Materias', 'Aulas', 'Semestres', 'Grupos', 'Alumnos', 'Docentes'],
        'docente'       => ['Mi horario', 'Calificaciones'],
        'alumno'        => ['Mi horario', 'Mis calificaciones'],
    ][$rol] ?? [];
@endphp

<a href="{{ route('panel.' . $rol) }}"
   @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs('panel.*')])>Inicio</a>

@foreach ($pendientes as $opcion)
    <span class="menu__opcion menu__opcion--pendiente">{{ $opcion }}</span>
@endforeach

<a href="{{ route('contrasena.editar') }}"
   @class(['menu__opcion', 'menu__opcion--activa' => request()->routeIs('contrasena.editar')])>Cambiar contraseña</a>
