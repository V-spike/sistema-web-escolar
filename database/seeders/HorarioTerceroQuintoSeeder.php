<?php

namespace Database\Seeders;

use App\Horarios\Hora;
use App\Horarios\RevisorEmpalmes;
use App\Models\Clase;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Semestre;
use Illuminate\Database\Seeder;

/**
 * Horario de ejemplo de 3.º y 5.º (1.º se acomoda a mano).
 * Pone clases de 50 minutos en los 7 espacios del día. Cada clase pasa por RevisorEmpalmes
 * antes de guardarse, igual que en la pantalla, así que nunca deja empalmes.
 *
 * Se puede correr solo, sin borrar lo demás:  php artisan db:seed --class=HorarioTerceroQuintoSeeder
 */
class HorarioTerceroQuintoSeeder extends Seeder
{
    /** Inicio de cada hora clase (la jornada es 15:00–21:20 con dos recesos) */
    private const ESPACIOS = ['15:00', '15:50', '16:40', '17:50', '18:40', '19:40', '20:30'];

    private RevisorEmpalmes $revisor;

    public function run(): void
    {
        $semestre = Semestre::activo();
        if (! $semestre) {
            return;
        }

        $this->revisor = new RevisorEmpalmes();
        $grupos = Grupo::where('semestre_id', $semestre->id)->whereIn('grado', [3, 5])->where('activo', true)
            ->orderBy('grado')->orderBy('letra')->get();

        // Solo se rehace 3.º y 5.º: las clases de 1.º no se tocan
        Clase::whereIn('grupo_id', $grupos->pluck('id'))->delete();

        foreach ($grupos as $g => $grupo) {
            // Primero las materias con más horas: son las más difíciles de acomodar
            $materias = Materia::where('activo', true)->where('semestre', $grupo->grado)->orderByDesc('horas_semana')->get();

            foreach ($materias as $materia) {
                $faltan = $materia->horas_semana;

                // Vuelta 1: máximo una hora de la materia por día, empezando en un día distinto por grupo
                // Vuelta 2 y 3: ya se permiten dos o tres horas el mismo día si no cupo de otra forma
                for ($maximoPorDia = 1; $maximoPorDia <= 3 && $faltan > 0; $maximoPorDia++) {
                    foreach ($this->diasEnOrden($g) as $dia) {
                        if ($faltan === 0) {
                            break;
                        }
                        if ($this->horasEseDia($grupo, $materia, $dia) >= $maximoPorDia) {
                            continue;
                        }
                        if ($this->poner($semestre, $grupo, $materia, $dia)) {
                            $faltan--;
                        }
                    }
                }
            }
        }
    }

    /** Intenta cada espacio del día con cada docente que tiene la materia; el de menos carga primero */
    private function poner(Semestre $semestre, Grupo $grupo, Materia $materia, int $dia): bool
    {
        $docentes = $materia->docentes()->where('activo', true)->withCount('clases')->orderBy('clases_count')->get();

        foreach (self::ESPACIOS as $inicio) {
            foreach ($docentes as $docente) {
                $datos = [
                    'semestre_id' => $semestre->id,
                    'grupo_id' => $grupo->id,
                    'materia_id' => $materia->id,
                    'docente_id' => $docente->id,
                    'aula_id' => $grupo->aula_id,
                    'dia' => $dia,
                    'hora_inicio' => $inicio,
                    'hora_fin' => Hora::aTexto(Hora::aMinutos($inicio) + 50),
                ];

                if ($this->revisor->revisar($datos)->sePuedeGuardar()) {
                    Clase::create($datos);

                    return true;
                }
            }
        }

        return false;
    }

    private function horasEseDia(Grupo $grupo, Materia $materia, int $dia): int
    {
        return Clase::where('grupo_id', $grupo->id)->where('materia_id', $materia->id)->where('dia', $dia)->count();
    }

    /** Lunes a viernes, empezando en un día distinto según el grupo, para repartir */
    private function diasEnOrden(int $g): array
    {
        return array_map(fn ($k) => (($k + $g) % 5) + 1, range(0, 4));
    }
}
