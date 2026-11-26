<?php

namespace Database\Seeders;

use App\Models\Calificacion;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Calificaciones de ejemplo de los parciales 1 y 2 para los alumnos de ejemplo.
 * Siempre salen las mismas (mt_srand), así la boleta se ve igual cada vez que se siembra.
 */
class CalificacionesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $semestre = Semestre::activo();
        if (! $semestre) {
            return;
        }

        mt_srand(2026);

        $materias = Materia::where('semestre', 1)->where('activo', true)->whereIn('tipo', Materia::EN_BOLETA)->get();
        $alumnos = User::where('rol', 'alumno')->where('activo', true)->whereNotNull('grupo_id')->orderBy('id')->get();

        Calificacion::where('semestre_id', $semestre->id)->delete();

        foreach ($alumnos as $alumno) {
            $nivel = mt_rand(60, 95) / 10;   // qué tan bien le va en general a este alumno

            foreach ($materias as $materia) {
                foreach ([1, 2] as $parcial) {
                    Calificacion::create([
                        'semestre_id' => $semestre->id,
                        'alumno_id' => $alumno->id,
                        'materia_id' => $materia->id,
                        'parcial' => $parcial,
                        'valor' => $this->valor($materia, $nivel),
                    ]);
                }
            }
        }
    }

    private function valor(Materia $materia, float $nivel): string
    {
        if ($materia->tipo === 'bloque_comun') {
            return mt_rand(1, 100) <= 92 ? 'A' : 'N/A';
        }

        if (mt_rand(1, 100) <= 3) {
            return 'NP';
        }

        $numero = max(5, min(10, $nivel + mt_rand(-15, 15) / 10));

        return Calificacion::normalizar((string) round($numero, 1), $materia);
    }
}
