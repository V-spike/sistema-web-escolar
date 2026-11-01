<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            ['name' => 'Superusuario de prueba', 'email' => 'superusuario@escolar.test', 'rol' => 'superusuario'],
            ['name' => 'Administrador de prueba', 'email' => 'administrador@escolar.test', 'rol' => 'administrador'],
            ['name' => 'Docente de prueba', 'email' => 'docente@escolar.test', 'rol' => 'docente'],
            ['name' => 'Alumno de prueba', 'email' => 'alumno@escolar.test', 'rol' => 'alumno'],
            ['name' => 'Cuenta dada de baja', 'email' => 'baja@escolar.test', 'rol' => 'alumno', 'activo' => false],
        ];

        foreach ($usuarios as $datos) {
            User::updateOrCreate(
                ['email' => $datos['email']],
                $datos + ['password' => 'Escolar2026', 'activo' => true]
            );
        }
    }
}