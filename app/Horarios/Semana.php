<?php

namespace App\Horarios;

use App\Models\Clase;
use App\Models\Jornada;
use App\Models\Receso;
use Illuminate\Support\Collection;

/**
 * Prepara los datos para dibujar una semana (lunes a viernes) con bloques puestos según su hora.
 * La vista componentes/semana.blade.php la dibuja.
 */
class Semana
{
    public int $entrada;
    public int $salida;
    public array $dias;
    /** @var array<int, array> bloques por día */
    public array $bloques = [];
    public array $recesos = [];
    public array $marcas = [];

    public function __construct(?Jornada $jornada = null)
    {
        $jornada ??= Jornada::actual();
        $this->entrada = $jornada->entrada;
        $this->salida = max($jornada->salida, $jornada->entrada + 60);
        $this->dias = config('escolar.dias');

        foreach (array_keys($this->dias) as $d) {
            $this->bloques[$d] = [];
        }

        foreach (Receso::orderBy('hora_inicio')->get() as $receso) {
            $this->recesos[] = $this->posicion($receso->inicio, $receso->fin) + ['texto' => $receso->nombre];
        }

        // Una marca por hora en punto, para la regla de la izquierda
        for ($m = (int) ceil($this->entrada / 60) * 60; $m < $this->salida; $m += 60) {
            $this->marcas[] = ['texto' => Hora::aTexto($m), 'arriba' => $this->porcentaje($m)];
        }
    }

    /** @param  Collection<Clase>  $clases */
    public function conClases(Collection $clases, string $mostrar = 'grupo', array $conProblema = []): self
    {
        foreach ($clases as $clase) {
            $lineas = match ($mostrar) {
                'docente' => [$clase->grupo->nombre, $clase->aula?->clave ?? 'Sin aula'],
                'aula' => [$clase->grupo->nombre, $clase->docente->nombre_completo],
                'alumno' => [$clase->docente->nombre_completo, $clase->aula?->clave ?? 'Sin aula'],
                default => [$clase->docente->nombre_completo, $clase->aula?->clave ?? 'Sin aula'],
            };

            $this->agregar($clase->dia, $clase->inicio, $clase->fin, [
                'titulo' => $clase->materia->clave_corta ?: $clase->materia->nombre,
                'nombre' => $clase->materia->nombre,
                'lineas' => $lineas,
                'rango' => $clase->rango,
                'tipo' => isset($conProblema[$clase->id]) ? 'problema' : 'clase',
                'color' => $clase->materia_id % 8,
                'id' => $clase->id,
            ]);
        }

        return $this;
    }

    /** Rangos simples (por ejemplo, la disponibilidad de un docente) */
    public function conRangos(Collection $rangos, string $texto, string $tipo = 'disponible'): self
    {
        foreach ($rangos as $rango) {
            $this->agregar($rango->dia, $rango->inicio, $rango->fin, [
                'titulo' => $texto,
                'nombre' => $texto,
                'lineas' => [],
                'rango' => $rango->rango,
                'tipo' => $tipo,
                'color' => 0,
                'id' => $rango->id,
            ]);
        }

        return $this;
    }

    private function agregar(int $dia, int $inicio, int $fin, array $datos): void
    {
        if (! isset($this->bloques[$dia])) {
            return;
        }
        $this->bloques[$dia][] = $this->posicion($inicio, $fin) + $datos;
    }

    private function posicion(int $inicio, int $fin): array
    {
        $inicio = max($inicio, $this->entrada);
        $fin = min($fin, $this->salida);

        return [
            'arriba' => $this->porcentaje($inicio),
            'alto' => max(0, $this->porcentaje($fin) - $this->porcentaje($inicio)),
        ];
    }

    private function porcentaje(int $minuto): float
    {
        return round(($minuto - $this->entrada) * 100 / ($this->salida - $this->entrada), 3);
    }

    /** Alto total en px: 1.4 px por minuto, para que una clase de 50 min se lea bien */
    public function altoPx(): int
    {
        return (int) (($this->salida - $this->entrada) * 1.4);
    }
}
