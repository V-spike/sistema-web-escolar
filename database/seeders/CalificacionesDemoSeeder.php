<?php

namespace Database\Seeders;

use App\Models\Calificacion;
use App\Models\ClaseImpartida;
use App\Models\Conducta;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Calificaciones de ejemplo de los parciales 1 y 2 para los alumnos de ejemplo (1.º, 3.º y 5.º).
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

        // Materias de la boleta agrupadas por grado: [1 => [...], 3 => [...], 5 => [...]]
        $materiasPorGrado = Materia::where('activo', true)->whereIn('tipo', Materia::EN_BOLETA)->get()->groupBy('semestre');
        $alumnos = User::with('grupo')->where('rol', 'alumno')->where('activo', true)->whereNotNull('grupo_id')->orderBy('id')->get();

        Calificacion::where('semestre_id', $semestre->id)->delete();
        ClaseImpartida::where('semestre_id', $semestre->id)->delete();
        Conducta::where('semestre_id', $semestre->id)->delete();

        $filas = [];
        $ahora = now();
        foreach ($alumnos as $alumno) {
            $nivel = mt_rand(60, 95) / 10;   // qué tan bien le va en general a este alumno

            foreach ($materiasPorGrado[$alumno->grupo->grado] ?? [] as $materia) {
                foreach ([1, 2] as $parcial) {
                    $filas[] = [
                        'semestre_id' => $semestre->id,
                        'alumno_id' => $alumno->id,
                        'materia_id' => $materia->id,
                        'parcial' => $parcial,
                        'valor' => $this->valor($materia, $nivel),
                        'faltas' => mt_rand(1, 100) <= 60 ? 0 : mt_rand(1, 3),
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ];
                }
            }

            // Conducta: casi todos buena
            $azar = mt_rand(1, 100);
            Conducta::create(['semestre_id' => $semestre->id, 'alumno_id' => $alumno->id,
                'valor' => $azar <= 85 ? 'buena' : ($azar <= 97 ? 'regular' : 'mala')]);
        }

        // Se guardan de 500 en 500: mucho más rápido que una por una
        foreach (array_chunk($filas, 500) as $bloque) {
            Calificacion::insert($bloque);
        }

        // Clases impartidas: unas 4 semanas por parcial × horas a la semana de la materia
        foreach ($alumnos->pluck('grupo')->unique('id') as $grupo) {
            foreach ($materiasPorGrado[$grupo->grado] ?? [] as $materia) {
                foreach ([1, 2] as $parcial) {
                    ClaseImpartida::create(['semestre_id' => $semestre->id, 'grupo_id' => $grupo->id,
                        'materia_id' => $materia->id, 'parcial' => $parcial, 'total' => $materia->horas_semana * 4]);
                }
            }
        }
    }

    private function valor(Materia $materia, float $nivel): string
    {
        if ($materia->tipo === 'acreditable') {
            return mt_rand(1, 100) <= 92 ? 'A' : 'N/A';
        }

        if (mt_rand(1, 100) <= 3) {
            return 'NP';
        }

        $numero = max(5, min(10, $nivel + mt_rand(-15, 15) / 10));

        return Calificacion::normalizar((string) round($numero, 1), $materia);
    }
}
