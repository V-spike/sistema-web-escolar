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

        // En el mismo periodo también cursan 3.º y 5.º (3 grupos cada uno, en el edificio B)
        $otros = [[3, 'A', 'B-1'], [3, 'B', 'B-2'], [3, 'C', 'B-3'], [5, 'A', 'B-4'], [5, 'B', 'B-5'], [5, 'C', 'B-6']];

        foreach ($otros as [$grado, $letra, $clave]) {
            Grupo::updateOrCreate(
                ['semestre_id' => $semestre->id, 'grado' => $grado, 'letra' => $letra],
                ['aula_id' => Aula::where('clave', $clave)->value('id'), 'capacidad' => 45, 'activo' => true]
            );
        }
    }
}