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
        $this->call(FranjaSeeder::class);
    }
}
