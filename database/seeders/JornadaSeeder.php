<?php

namespace Database\Seeders;

use App\Models\Jornada;
use App\Models\Receso;
use Illuminate\Database\Seeder;

class JornadaSeeder extends Seeder
{
    /**
     * Turno vespertino: 15:00 a 21:20, hora clase de 50 minutos y dos recesos.
     */
    public function run(): void
    {
        $jornada = Jornada::query()->first() ?? new Jornada();
        $jornada->fill(['hora_entrada' => '15:00', 'hora_salida' => '21:20', 'minutos_por_hora' => 50])->save();

        Receso::updateOrCreate(['nombre' => 'Receso 1'], ['hora_inicio' => '17:30', 'hora_fin' => '17:50']);
        Receso::updateOrCreate(['nombre' => 'Receso 2'], ['hora_inicio' => '19:30', 'hora_fin' => '19:40']);
    }
}
