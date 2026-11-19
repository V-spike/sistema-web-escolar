<?php

namespace Database\Seeders;

use App\Models\Franja;
use Illuminate\Database\Seeder;

class FranjaSeeder extends Seeder
{
    /**
     * Jornada real del turno vespertino: 7 franjas de 50 min y 2 recesos.
     * 7 franjas × 5 días = 35 espacios por grupo a la semana.
     */
    public function run(): void
    {
        $franjas = [
            [1, '15:00', '15:50', false],
            [2, '15:50', '16:40', false],
            [3, '16:40', '17:30', false],
            [4, '17:30', '17:50', true],
            [5, '17:50', '18:40', false],
            [6, '18:40', '19:30', false],
            [7, '19:30', '19:40', true],
            [8, '19:40', '20:30', false],
            [9, '20:30', '21:20', false],
        ];

        foreach ($franjas as [$orden, $inicio, $fin, $receso]) {
            Franja::updateOrCreate(
                ['orden' => $orden],
                ['hora_inicio' => $inicio, 'hora_fin' => $fin, 'es_receso' => $receso]
            );
        }
    }
}
