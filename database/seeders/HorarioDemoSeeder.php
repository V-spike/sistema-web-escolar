<?php

namespace Database\Seeders;

use App\Horarios\Hora;
use App\Horarios\RevisorEmpalmes;
use App\Models\Clase;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Horario de ejemplo para la demo: acomoda 1.º A a 1.º F en espacios de 50 minutos
 * y deja 1.º G vacío para acomodarlo a mano frente al profe.
 * Cada clase pasa por RevisorEmpalmes antes de guardarse, igual que en la pantalla.
 */
class HorarioDemoSeeder extends Seeder
{
    private const ESPACIOS = ['15:00', '15:50', '16:40', '17:50', '18:40', '19:40', '20:30'];

    public function run(): void
    {
        $semestre = Semestre::activo();
        if (! $semestre) {
            return;
        }

        // La tutoría la da el docente de cada grupo con más horas libres: se agrega a 7 perfiles
        $tutoria = Materia::where('clave', 'TUT1')->first();
        if ($tutoria) {
            foreach (Docente::orderByDesc('horas_plaza')->take(7)->get() as $docente) {
                $docente->materias()->syncWithoutDetaching([$tutoria->id]);
            }
        }

        // Cuentas de prueba ligadas: el docente de prueba es D-001 y el alumno es de 1.º A
        $d001 = Docente::where('numero_personal', 'D-001')->first();
        $usuarioDocente = User::where('email', 'docente@escolar.test')->first();
        if ($d001 && $usuarioDocente) {
            Docente::where('user_id', $usuarioDocente->id)->update(['user_id' => null]);
            $d001->update(['user_id' => $usuarioDocente->id]);
        }
        $primeroA = Grupo::where('semestre_id', $semestre->id)->where('grado', 1)->where('letra', 'A')->first();
        User::where('email', 'alumno@escolar.test')->update(['grupo_id' => $primeroA?->id, 'matricula' => 'A-0001']);

        Clase::where('semestre_id', $semestre->id)->delete();

        $revisor = new RevisorEmpalmes();
        $grupos = Grupo::where('semestre_id', $semestre->id)->where('grado', 1)->whereIn('letra', ['A', 'B', 'C', 'D', 'E', 'F'])
            ->orderBy('letra')->get();
        $materias = Materia::where('activo', true)->where('semestre', 1)->where('tipo', '!=', 'bloque_comun')
            ->orderByDesc('horas_semana')->get();

        foreach ($grupos as $g => $grupo) {
            foreach ($materias as $materia) {
                $docentes = $materia->docentes()->where('activo', true)->get()->shuffle(crc32($grupo->letra . $materia->clave));
                $puestas = 0;
                $porDia = [];

                // Recorre los días empezando en uno distinto por grupo, para repartir
                for ($vuelta = 0; $vuelta < 2 && $puestas < $materia->horas_semana; $vuelta++) {
                    foreach ([1, 2, 3, 4, 5] as $k) {
                        $dia = (($k + $g + $vuelta) % 5) + 1;
                        if ($puestas >= $materia->horas_semana || ($porDia[$dia] ?? 0) > $vuelta) {
                            continue;
                        }

                        if ($this->poner($revisor, $semestre, $grupo, $materia, $docentes, $dia)) {
                            $puestas++;
                            $porDia[$dia] = ($porDia[$dia] ?? 0) + 1;
                        }
                    }
                }
            }
        }
    }

    private function poner(RevisorEmpalmes $revisor, Semestre $semestre, Grupo $grupo, Materia $materia, $docentes, int $dia): bool
    {
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

                if ($revisor->revisar($datos)->sePuedeGuardar()) {
                    Clase::create($datos);

                    return true;
                }
            }
        }

        return false;
    }
}
