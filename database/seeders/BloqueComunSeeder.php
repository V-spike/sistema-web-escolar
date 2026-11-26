<?php

namespace Database\Seeders;

use App\Models\BloqueComun;
use App\Models\Franja;
use App\Models\Materia;
use App\Models\Semestre;
use Illuminate\Database\Seeder;

class BloqueComunSeeder extends Seeder
{
    /**
     * Currículum Ampliado de 1.º: lunes, franjas 5 y 6 (17:50 a 19:30).
     * Necesita el semestre activo, la materia CA1 (T-40) y las franjas (T-43).
     */
    public function run(): void
    {
        $semestre = Semestre::activo();
        $materia = Materia::where('clave', 'CA1')->first();

        if (! $semestre || ! $materia) {
            return;
        }

        foreach (Franja::whereIn('orden', [5, 6])->get() as $franja) {
            BloqueComun::updateOrCreate(
                ['semestre_id' => $semestre->id, 'grado' => 1, 'dia' => 1, 'franja_id' => $franja->id],
                ['materia_id' => $materia->id]
            );
        }
    }
}
