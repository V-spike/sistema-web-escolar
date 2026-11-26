<?php

namespace Database\Seeders;

use App\Models\BloqueComun;
use App\Models\Materia;
use App\Models\Semestre;
use Illuminate\Database\Seeder;

class BloqueComunSeeder extends Seeder
{
    /**
     * Currículum Ampliado de 1.º: lunes de 17:50 a 19:30.
     */
    public function run(): void
    {
        $semestre = Semestre::activo();
        $materia = Materia::where('clave', 'CA1')->first();

        if (! $semestre || ! $materia) {
            return;
        }

        BloqueComun::updateOrCreate(
            ['semestre_id' => $semestre->id, 'grado' => 1, 'dia' => 1, 'materia_id' => $materia->id],
            ['hora_inicio' => '17:50', 'hora_fin' => '19:30']
        );
    }
}
