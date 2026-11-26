<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Alumnos de ejemplo para los grupos de 1.º A a 1.º F.
 * Los nombres y las CURP son INVENTADOS: nunca se cargan datos de alumnos reales al repositorio.
 * Todos entran con la contraseña Escolar2026 (correo alumno.a01@escolar.test, alumno.b07@..., etc.).
 */
class AlumnosSeeder extends Seeder
{
    private const NOMBRES = [
        ['Sofía', 'M'], ['Mateo', 'H'], ['Valentina', 'M'], ['Santiago', 'H'], ['Regina', 'M'],
        ['Leonardo', 'H'], ['Camila', 'M'], ['Emiliano', 'H'], ['Ximena', 'M'], ['Diego', 'H'],
        ['Renata', 'M'], ['Sebastián', 'H'], ['Fernanda', 'M'], ['Iker', 'H'], ['Daniela', 'M'],
    ];

    private const APELLIDOS = [
        'Hernández', 'García', 'Martínez', 'López', 'González', 'Pérez', 'Rodríguez', 'Sánchez',
        'Ramírez', 'Cruz', 'Flores', 'Gómez', 'Morales', 'Vázquez', 'Reyes', 'Jiménez',
    ];

    public function run(): void
    {
        $semestre = Semestre::activo();
        if (! $semestre) {
            return;
        }

        // Una sola vez: calcular el hash para 90 alumnos tardaría mucho
        $password = Hash::make('Escolar2026');

        $grupos = Grupo::where('semestre_id', $semestre->id)->where('grado', 1)
            ->whereIn('letra', ['A', 'B', 'C', 'D', 'E', 'F'])->orderBy('letra')->get();

        foreach ($grupos as $g => $grupo) {
            foreach (self::NOMBRES as $i => [$nombre, $sexo]) {
                // Apellidos distintos en cada grupo, siempre los mismos al volver a sembrar
                $paterno = self::APELLIDOS[($i * 3 + $g) % count(self::APELLIDOS)];
                $materno = self::APELLIDOS[($i * 5 + $g * 7 + 1) % count(self::APELLIDOS)];
                $numero = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                $letra = strtolower($grupo->letra);

                User::updateOrCreate(
                    ['email' => "alumno.{$letra}{$numero}@escolar.test"],
                    [
                        'name' => "{$paterno} {$materno} {$nombre}",
                        'matricula' => '1' . $grupo->letra . $numero,
                        'curp' => $this->curpInventada($paterno, $materno, $nombre, $sexo, $g * 20 + $i),
                        'password' => $password,
                        'rol' => 'alumno',
                        'activo' => true,
                        'grupo_id' => $grupo->id,
                    ]
                );
            }
        }

        // El alumno de prueba también va en 1.º A, con CURP inventada
        User::where('email', 'alumno@escolar.test')->update([
            'grupo_id' => $grupos->first()?->id,
            'matricula' => 'A-0001',
            'curp' => 'PRUE100101HVZRBLA0',
        ]);
    }

    /** CURP con la forma correcta (18 caracteres) pero inventada */
    private function curpInventada(string $paterno, string $materno, string $nombre, string $sexo, int $n): string
    {
        $quitar = fn ($t) => strtoupper(strtr($t, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'Á' => 'A']));
        [$p, $m, $no] = [$quitar($paterno), $quitar($materno), $quitar($nombre)];

        $fecha = sprintf('10%02d%02d', 1 + $n % 12, 1 + $n % 28);   // nacidos en 2010

        return substr($p, 0, 1) . 'X' . substr($m, 0, 1) . substr($no, 0, 1) . $fecha . $sexo . 'VZ' . 'XXX' . 'A' . ($n % 10);
    }
}
