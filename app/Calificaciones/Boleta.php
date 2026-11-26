<?php

namespace App\Calificaciones;

use App\Models\Calificacion;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\User;

/**
 * Arma los datos de la boleta de un alumno en un semestre:
 * una fila por materia con sus parciales, el promedio y la calificación final con letra.
 * La vista solo dibuja lo que esta clase calcula.
 */
class Boleta
{
    public array $filas = [];
    public array $promedioParcial = [];   // [1 => 8.2, 2 => 7.9, 3 => null]
    public ?float $promedioGeneral = null;
    public string $tipoExamen = '';       // "2P" = la boleta va hasta el segundo parcial

    public function __construct(public User $alumno, public Semestre $semestre)
    {
        $parciales = range(1, config('escolar.parciales'));
        $grado = $alumno->grupo?->grado;

        // Materias del grado que van en la boleta: primero las normales, al final las acreditables (C.A.)
        $materias = Materia::where('semestre', $grado)->where('activo', true)
            ->whereIn('tipo', Materia::EN_BOLETA)
            ->orderByRaw("tipo = 'acreditable'")->orderBy('id')->get();

        $calificaciones = Calificacion::where('semestre_id', $semestre->id)->where('alumno_id', $alumno->id)->get();

        foreach ($materias as $materia) {
            $valores = [];
            foreach ($parciales as $p) {
                $valores[$p] = $calificaciones->first(fn ($c) => $c->materia_id === $materia->id && $c->parcial === $p)?->valor;
            }

            $this->filas[] = $this->fila($materia, $valores);
        }

        // Promedio de cada parcial: solo materias con número (las acreditables no promedian)
        foreach ($parciales as $p) {
            $numeros = collect($this->filas)->map(fn ($f) => Calificacion::comoNumero($f['parciales'][$p]))->filter(fn ($n) => $n !== null);
            $this->promedioParcial[$p] = $numeros->isEmpty() ? null : round($numeros->avg(), 1);

            if ($numeros->isNotEmpty()) {
                $this->tipoExamen = "{$p}P";
            }
        }

        // Promedio general: cuando todas las materias normales ya tienen calificación final
        $finales = collect($this->filas)->where('tipo', 'normal')->pluck('final');
        if ($finales->isNotEmpty() && $finales->every(fn ($f) => $f !== null)) {
            $this->promedioGeneral = round($finales->avg(), 1);
        }
    }

    private function fila(Materia $materia, array $valores): array
    {
        $completa = collect($valores)->every(fn ($v) => $v !== null);
        $final = null;
        $finalTexto = '';

        if ($completa && $materia->tipo === 'acreditable') {
            // Acredita si acreditó el último parcial
            $finalTexto = end($valores);
        } elseif ($completa) {
            $final = round(collect($valores)->map(fn ($v) => Calificacion::comoNumero($v))->avg(), 1);
            $finalTexto = (string) $final;
        }

        return [
            'materia' => $materia,
            'tipo' => $materia->tipo,
            'parciales' => $valores,
            'final' => $final,
            'final_texto' => $finalTexto,
            'letra' => $materia->tipo === 'acreditable'
                ? ($finalTexto === 'A' ? 'ACREDITADO' : ($finalTexto ? 'NO ACREDITADO' : ''))
                : Letra::de($final),
        ];
    }

    /** "I", "II", "III"... para el semestre y los parciales */
    public static function romano(int $n): string
    {
        return ['', 'I', 'II', 'III', 'IV', 'V', 'VI'][$n] ?? (string) $n;
    }
}
