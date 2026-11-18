<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Materia;

class CatalogosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $materias = [
        ['clave' => 'SCA-1025', 'nombre' => 'Arquitectura de Computadoras', 'horas_semana' => 4, 'activo' => true],
        ['clave' => 'AEB-1055', 'nombre' => 'Programación Lógica y Funcional', 'horas_semana' => 4, 'activo' => true],
        ['clave' => 'AEB-1011', 'nombre' => 'Desarrollo de Aplicaciones Móviles', 'horas_semana' => 5, 'activo' => true],
    ];

    foreach ($materias as $datos) {
        Materia::updateOrCreate(
            ['clave' => $datos['clave']], 
            $datos
        );
    }
}
}
