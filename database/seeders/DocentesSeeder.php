<?php

namespace Database\Seeders;

use App\Models\Docente;
use App\Models\Materia;
use Illuminate\Database\Seeder;

/**
 * Carga la plantilla docente desde database/seeders/datos/docentes.csv
 * (sale de "Docentes Horas Perfil.xlsx": nombre, horas y formación).
 * La columna "materias" lleva las claves que puede impartir, separadas por espacio.
 */
class DocentesSeeder extends Seeder
{
    public function run(): void
    {
        $archivo = fopen(database_path('seeders/datos/docentes.csv'), 'r');
        $columnas = fgetcsv($archivo, null, ',', '"', '');   // primera fila: nombres de columna

        while (($fila = fgetcsv($archivo, null, ',', '"', '')) !== false) {
            $d = array_combine($columnas, $fila);

            $docente = Docente::updateOrCreate(
                ['numero_personal' => $d['numero_personal']],
                [
                    'apellido_paterno' => $d['apellido_paterno'],
                    'apellido_materno' => $d['apellido_materno'] ?: null,
                    'nombre'           => $d['nombre'],
                    'horas_plaza'      => (int) $d['horas_plaza'],
                    'licenciatura'     => $d['licenciatura'] ?: null,
                    'especialidad'     => $d['especialidad'] ?: null,
                    'maestria'         => $d['maestria'] ?: null,
                    'doctorado'        => $d['doctorado'] ?: null,
                    'activo'           => true,
                ]
            );

            $claves = array_filter(explode(' ', $d['materias']));
            $docente->materias()->sync(Materia::whereIn('clave', $claves)->pluck('id'));
        }

        fclose($archivo);
    }
}
