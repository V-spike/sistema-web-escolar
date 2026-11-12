<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aula;

class CatalogosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aulas = [
            ['clave' => 'A-101', 'edificio' => 'Edificio A', 'capacidad' => 40, 'activo' => true],
            ['clave' => 'A-102', 'edificio' => 'Edificio A', 'capacidad' => 35, 'activo' => true],
            ['clave' => 'LC-1', 'edificio' => 'Laboratorio de cómputo', 'capacidad' => 30, 'activo' => true],
        ];

        foreach ($aulas as $datos) {
            Aula::updateOrCreate(
                ['clave' => $datos['clave']],
                $datos
            );
        }
    }
}