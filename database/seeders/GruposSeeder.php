<?php

namespace Database\Seeders;

use App\Models\Aula;
use App\Models\Grupo;
use App\Models\Semestre;
use Illuminate\Database\Seeder;

class GruposSeeder extends Seeder
{
    public function run(): void
    {
        $semestre = Semestre::where('activo', true)->first();

        if (! $semestre) {
            return;
        }

        $letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach ($letras as $indice => $letra) {
            $aula = Aula::where('clave', 'S-' . ($indice + 1))->first();

            Grupo::updateOrCreate(
                [
                    'semestre_id' => $semestre->id,
                    'grado'       => 1,
                    'letra'       => $letra,
                ],
                [
                    'aula_id'   => $aula?->id,
                    'capacidad' => 50,
                    'activo'    => true,
                ]
            );
        }
    }
}