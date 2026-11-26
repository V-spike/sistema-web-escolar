<?php

namespace Database\Seeders;

use App\Models\Docente;
use Illuminate\Database\Seeder;

class DisponibilidadSeeder extends Seeder
{
    /**
     * Disponibilidad de ejemplo: la mayoría toda la jornada de lunes a viernes;
     * algunos docentes de pocas horas solo ciertos días o desde cierta hora,
     * para que en la demo se vea el aviso de "no está disponible".
     */
    public function run(): void
    {
        $parciales = [
            'D-009' => [[2, '15:00', '19:30'], [4, '15:00', '19:30']],
            'D-010' => [[1, '15:00', '17:30'], [3, '15:00', '17:30'], [5, '15:00', '17:30']],
            'D-014' => [[2, '17:50', '21:20'], [4, '17:50', '21:20']],
        ];

        foreach (Docente::all() as $docente) {
            $docente->disponibilidades()->delete();
            $rangos = $parciales[$docente->numero_personal]
                ?? array_map(fn ($dia) => [$dia, '15:00', '21:20'], [1, 2, 3, 4, 5]);

            foreach ($rangos as [$dia, $inicio, $fin]) {
                $docente->disponibilidades()->create(['dia' => $dia, 'hora_inicio' => $inicio, 'hora_fin' => $fin]);
            }
        }
    }
}
