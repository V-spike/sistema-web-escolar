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
            ['clave' => 'PYCC1',  'clave_corta' => 'PyCC',   'nombre' => 'Práctica y Colaboración Ciudadana',                 'semestre' => 1, 'horas_semana' => 2, 'tipo' => 'normal'],
            ['clave' => 'CA1',    'clave_corta' => 'C.A.',   'nombre' => 'Currículum Ampliado (clubes)',                      'semestre' => 1, 'horas_semana' => 2, 'tipo' => 'bloque_comun'],
            ['clave' => 'TUT1',   'clave_corta' => 'TUT',    'nombre' => 'Tutoría académica',                                 'semestre' => 1, 'horas_semana' => 5, 'tipo' => 'tutoria'],
        ];

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
            ['clave' => 'LAB-C', 'edificio' => 'Laboratorio de ciencias', 'capacidad' => 40,  'tipo' => 'laboratorio'],
            ['clave' => 'AUD',   'edificio' => 'Auditorio',               'capacidad' => 200, 'tipo' => 'auditorio'],
        ];

        foreach ($aulas as $datos) {
            Aula::updateOrCreate(['clave' => $datos['clave']], $datos);
        }
    }
}
