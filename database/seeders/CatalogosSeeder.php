<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;
use App\Models\Aula;

class CatalogosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materias = [
            ['clave' => 'CNEYT1', 'clave_corta' => 'CNEyT1', 'nombre' => 'Ciencias Naturales, Experimentales y Tecnología I', 'semestre' => 1, 'horas_semana' => 4, 'tipo' => 'normal'],
            ['clave' => 'CS1',    'clave_corta' => 'C.S1',   'nombre' => 'Ciencias Sociales I',                               'semestre' => 1, 'horas_semana' => 2, 'tipo' => 'normal'],
            ['clave' => 'CD1',    'clave_corta' => 'CDI1',   'nombre' => 'Cultura Digital I',                                 'semestre' => 1, 'horas_semana' => 3, 'tipo' => 'normal'],
            ['clave' => 'PM1',    'clave_corta' => 'P.M1',   'nombre' => 'Pensamiento Matemático I',                          'semestre' => 1, 'horas_semana' => 4, 'tipo' => 'normal'],
            ['clave' => 'LYC1',   'clave_corta' => 'LYC1',   'nombre' => 'Lengua y Comunicación I',                           'semestre' => 1, 'horas_semana' => 3, 'tipo' => 'normal'],
            ['clave' => 'PFYH1',  'clave_corta' => 'PFyH1',  'nombre' => 'Pensamiento Filosófico y Humanidades I',            'semestre' => 1, 'horas_semana' => 4, 'tipo' => 'normal'],
            ['clave' => 'ING1',   'clave_corta' => 'ING1',   'nombre' => 'Inglés I',                                          'semestre' => 1, 'horas_semana' => 3, 'tipo' => 'normal'],
            ['clave' => 'LDI1',   'clave_corta' => 'L.DI',   'nombre' => 'Laboratorio de Investigación',                      'semestre' => 1, 'horas_semana' => 3, 'tipo' => 'normal'],
            ['clave' => 'PYCC1',  'clave_corta' => 'PyCC',   'nombre' => 'Práctica y Colaboración Ciudadana',                 'semestre' => 1, 'horas_semana' => 2, 'tipo' => 'extendido'],
            ['clave' => 'CA1',    'clave_corta' => 'C.A.',   'nombre' => 'Currículum Ampliado',                              'semestre' => 1, 'horas_semana' => 2, 'tipo' => 'acreditable'],
            ['clave' => 'TUT1',   'clave_corta' => 'TUT',    'nombre' => 'Tutoría académica',                                 'semestre' => 1, 'horas_semana' => 5, 'tipo' => 'tutoria'],
        ];

        // 3.º y 5.º semestre: materias DE EJEMPLO para probar el sistema con varios grados a la vez.
        // No son el plan oficial; se cambian en Materias cuando se tenga el plan real. 35 h por grado, como 1.º.
        // [clave, clave corta, nombre, horas, tipo]
        $ejemplo = [
            3 => [
                ['CNEYT3', 'CNEyT3', 'Ciencias Naturales, Experimentales y Tecnología III', 4, 'normal'],
                ['CS3',    'C.S3',   'Ciencias Sociales III', 2, 'normal'],
                ['PM3',    'P.M3',   'Pensamiento Matemático III', 4, 'normal'],
                ['LYC3',   'LYC3',   'Lengua y Comunicación III', 3, 'normal'],
                ['ING3',   'ING3',   'Inglés III', 3, 'normal'],
                ['HUM2',   'HUM2',   'Humanidades II', 4, 'normal'],
                ['CH1',    'C.H1',   'Conciencia Histórica I', 3, 'normal'],
                ['LDI3',   'L.DI3',  'Laboratorio de Investigación III', 3, 'normal'],
                ['PYCC3',  'PyCC3',  'Práctica y Colaboración Ciudadana III', 2, 'extendido'],
                ['CA3',    'C.A.3',  'Currículum Ampliado III', 2, 'acreditable'],
                ['TUT3',   'TUT3',   'Tutoría académica III', 5, 'tutoria'],
            ],
            5 => [
                ['CNEYT5', 'CNEyT5', 'Ciencias Naturales, Experimentales y Tecnología V', 4, 'normal'],
                ['CS5',    'C.S5',   'Ciencias Sociales V', 2, 'normal'],
                ['TSM1',   'TSM1',   'Temas Selectos de Matemáticas I', 4, 'normal'],
                ['TLR1',   'TLR1',   'Taller de Lectura y Redacción', 3, 'normal'],
                ['ING5',   'ING5',   'Inglés V', 3, 'normal'],
                ['HUM4',   'HUM4',   'Humanidades IV', 4, 'normal'],
                ['CH3',    'C.H3',   'Conciencia Histórica III', 3, 'normal'],
                ['PVE1',   'PVE1',   'Proyecto de Vida y Emprendimiento', 3, 'normal'],
                ['PYCC5',  'PyCC5',  'Práctica y Colaboración Ciudadana V', 2, 'extendido'],
                ['CA5',    'C.A.5',  'Currículum Ampliado V', 2, 'acreditable'],
                ['TUT5',   'TUT5',   'Tutoría académica V', 5, 'tutoria'],
            ],
        ];

        foreach ($ejemplo as $semestre => $lista) {
            foreach ($lista as [$clave, $corta, $nombre, $horas, $tipo]) {
                $materias[] = ['clave' => $clave, 'clave_corta' => $corta, 'nombre' => $nombre,
                    'semestre' => $semestre, 'horas_semana' => $horas, 'tipo' => $tipo];
            }
        }

        foreach ($materias as $datos) {
            Materia::updateOrCreate(['clave' => $datos['clave']], $datos);
        }

        $aulas = [
            ['clave' => 'S-1',   'edificio' => 'Edificio principal',      'capacidad' => 50,  'tipo' => 'salon'],
            ['clave' => 'S-2',   'edificio' => 'Edificio principal',      'capacidad' => 50,  'tipo' => 'salon'],
            ['clave' => 'S-3',   'edificio' => 'Edificio principal',      'capacidad' => 50,  'tipo' => 'salon'],
            ['clave' => 'S-4',   'edificio' => 'Edificio principal',      'capacidad' => 50,  'tipo' => 'salon'],
            ['clave' => 'S-5',   'edificio' => 'Edificio principal',      'capacidad' => 50,  'tipo' => 'salon'],
            ['clave' => 'S-6',   'edificio' => 'Edificio principal',      'capacidad' => 50,  'tipo' => 'salon'],
            ['clave' => 'S-7',   'edificio' => 'Edificio principal',      'capacidad' => 50,  'tipo' => 'salon'],
            // Salones del edificio B para los grupos de 3.º y 5.º
            ['clave' => 'B-1',   'edificio' => 'Edificio B',              'capacidad' => 45,  'tipo' => 'salon'],
            ['clave' => 'B-2',   'edificio' => 'Edificio B',              'capacidad' => 45,  'tipo' => 'salon'],
            ['clave' => 'B-3',   'edificio' => 'Edificio B',              'capacidad' => 45,  'tipo' => 'salon'],
            ['clave' => 'B-4',   'edificio' => 'Edificio B',              'capacidad' => 45,  'tipo' => 'salon'],
            ['clave' => 'B-5',   'edificio' => 'Edificio B',              'capacidad' => 45,  'tipo' => 'salon'],
            ['clave' => 'B-6',   'edificio' => 'Edificio B',              'capacidad' => 45,  'tipo' => 'salon'],
            ['clave' => 'LAB-C', 'edificio' => 'Laboratorio de ciencias', 'capacidad' => 40,  'tipo' => 'laboratorio'],
            ['clave' => 'AUD',   'edificio' => 'Auditorio',               'capacidad' => 200, 'tipo' => 'auditorio'],
        ];

        foreach ($aulas as $datos) {
            Aula::updateOrCreate(['clave' => $datos['clave']], $datos);
        }
    }
}
