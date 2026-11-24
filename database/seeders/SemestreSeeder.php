<?php

namespace Database\Seeders;

use App\Models\Semestre;
use Illuminate\Database\Seeder;

class SemestreSeeder extends Seeder
{
    /**
     * Periodo de ejemplo. Va antes que los grupos porque ellos lo necesitan.
     */
    public function run(): void
    {
        Semestre::updateOrCreate(
            ['nombre' => '2026-2027 A'],
            ['fecha_inicio' => '2026-08-24', 'fecha_fin' => '2027-01-22', 'activo' => true]
        );
    }
}
