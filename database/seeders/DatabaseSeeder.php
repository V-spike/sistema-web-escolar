<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsuariosSeeder::class);
        $this->call(SemestreSeeder::class);
        $this->call(CatalogosSeeder::class);
        $this->call(JornadaSeeder::class);
        $this->call(BloqueComunSeeder::class);
        $this->call(DocentesSeeder::class);
        $this->call(GruposSeeder::class);
        $this->call(DisponibilidadSeeder::class);
        $this->call(AlumnosSeeder::class);        // alumnos inventados de 1.º A a 1.º F

        // El horario de ejemplo tarda unos segundos: en las pruebas automáticas no se carga
        if (! app()->runningUnitTests()) {
            $this->call(HorarioDemoSeeder::class);
            $this->call(CalificacionesDemoSeeder::class);
        }
    }
}
