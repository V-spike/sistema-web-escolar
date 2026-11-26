<?php

/*
| Datos fijos de la escuela que usan varias pantallas.
| Se leen con config('escolar.dias'), por ejemplo.
| En la base de datos el día se guarda como número (1 a 5); el nombre sale de aquí.
*/

return [
    // Datos del plantel, tal como salen en la boleta oficial
    'escuela' => [
        'nombre' => 'Lic. Antonio María de Rivera',
        'turno' => 'Vespertino',
        'clave' => '30EBH0389X',
        'localidad' => 'Xalapa, Veracruz',
        'municipio' => 'Xalapa',
        'modalidad' => 'Escolarizada',
        'direccion_general' => 'Dirección General de Bachillerato',
        // Firmas de la boleta: se ponen en el archivo .env para no subir nombres al repositorio
        'director' => env('ESCUELA_DIRECTOR', 'Director(a) del plantel'),
        'subdirector' => env('ESCUELA_SUBDIRECTOR', 'Subdirector(a) del plantel'),
    ],

    // Parciales del semestre (la boleta muestra I, II y III)
    'parciales' => 3,

    'dias' => [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
    ],
];
