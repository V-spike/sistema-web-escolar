<?php

namespace Database\Seeders;

use App\Models\Docente;
use App\Models\Materia;
use Illuminate\Database\Seeder;

class DocentesSeeder extends Seeder
{
    public function run(): void
    {
        // [num, apellido_paterno, apellido_materno, nombre, horas_plaza, [claves de materias]]
        $docentes = [
            ['D-001', 'Acosta', 'Rebolledo', 'Isaí Rubén', 40, ['PFYH1']],
            ['D-002', 'Aguilar', 'Figueroa', 'Iris del Carmen', 20, ['LDI1']],
            ['D-003', 'Ahumada', 'Abdala', 'Adriana', 30, ['CD1']],
            ['D-004', 'Álvarez', 'Fernández', 'Lucía Piedad', 20, ['CNEYT1']],
            ['D-005', 'Andrade', 'Melgarejo', 'Octaviano', 20, ['ING1']],
            ['D-006', 'Arellano', 'Martínez', 'Xóchitl', 11, ['ING1']],
            ['D-007', 'Baldoneyro', 'Domínguez', 'Anahí', 40, ['CD1']],
            ['D-008', 'Breling', 'Ramos', 'Artur Alfons', 15, ['PM1']],
            ['D-009', 'Cruz', 'Muñoz', 'Miguel Ángel', 6, ['LDI1']],
            ['D-010', 'De la Fuente', 'Ramírez', 'Elizabeth', 5, ['LYC1']],
            ['D-011', 'Domínguez', 'Absalón', 'Carlos Daniel', 10, ['CD1']],
            ['D-012', 'Fernández', 'Pérez', 'Emilio', 20, ['LDI1']],
            ['D-013', 'Fernández', 'Pérez', 'Narda', 7, ['CS1']],
            ['D-014', 'Gasperín', 'Hernández', 'Erika', 5, ['PYCC1']],
            ['D-015', 'Hernández', 'Suárez', 'Alma Ivetth', 20, ['CNEYT1']],
            ['D-016', 'Landa', 'Castellanos', 'Roxana', 15, ['PYCC1']],
            ['D-017', 'Larruz', 'Hernández', 'Mónica', 16, ['PM1']],
            ['D-018', 'Martínez', 'Luján', 'Tonatiuh Vicente', 30, ['PYCC1']],
            ['D-019', 'Peredo', 'Márquez', 'María Luisa', 12, ['CS1']],
            ['D-020', 'Ramírez', 'Romero', 'Yair Josué', 13, ['CS1']],
            ['D-021', 'Rivera', 'Schacht', 'Miguel Darío', 20, ['PFYH1']],
            ['D-022', 'Rodríguez', 'Guzmán', 'Martha Patricia', 12, ['ING1']],
            ['D-023', 'Rodríguez', 'Villanueva', 'Filomeno', 10, ['PM1']],
            ['D-024', 'Santiago', 'Antonio', 'Leticia', 30, ['PM1']],
            ['D-025', 'Sardiña', 'García', 'Iván', 40, ['LYC1']],
            ['D-026', 'Segovia', 'Montini', 'Juan Antonio', 22, ['PFYH1']],
            ['D-027', 'Solano', 'López', 'Tayde', 20, ['PYCC1']],
            ['D-028', 'Vásquez', 'Ortega', 'Hiri Roxana', 20, ['LYC1']],
        ];

        foreach ($docentes as [$numero, $paterno, $materno, $nombre, $horas, $claves]) {
            $docente = Docente::updateOrCreate(
                ['numero_personal' => $numero],
                [
                    'apellido_paterno' => $paterno,
                    'apellido_materno' => $materno,
                    'nombre'           => $nombre,
                    'horas_plaza'      => $horas,
                    'activo'           => true,
                ]
            );

            $materiaIds = Materia::whereIn('clave', $claves)->pluck('id');
            $docente->materias()->sync($materiaIds);
        }
    }
}