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

        // El horario NO se llena: se acomoda a mano en "Acomodar horario".
        // Las calificaciones de ejemplo tardan unos segundos: en las pruebas automáticas no se cargan
        if (! app()->runningUnitTests()) {
            $this->call(CalificacionesDemoSeeder::class);
        }
    }
}
