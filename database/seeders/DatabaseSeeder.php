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
        $this->call(DocentesSeeder::class);
        $this->call(GruposSeeder::class);
        $this->call(DisponibilidadSeeder::class);
        $this->call(AlumnosSeeder::class);        // alumnos inventados de 1.º A a 1.º F

        // 1.º se acomoda a mano; 3.º y 5.º traen horario de ejemplo (tarda cerca de un minuto).
        // Ni el horario ni las calificaciones de ejemplo se cargan en las pruebas automáticas.
        if (! app()->runningUnitTests()) {
            $this->call(HorarioTerceroQuintoSeeder::class);
            $this->call(CalificacionesDemoSeeder::class);
        }
    }
}
