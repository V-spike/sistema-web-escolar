<?php

namespace App\Horarios;

/**
 * Convierte horas "HH:MM" a minutos desde la medianoche y al revés.
 * Comparar minutos (números) es más seguro que comparar textos de hora.
 */
class Hora
{
    public static function aMinutos(?string $hora): ?int
    {
        if ($hora === null || ! preg_match('/^(\d{1,2}):(\d{2})/', $hora, $m)) {
            return null;
        }

        return ((int) $m[1]) * 60 + (int) $m[2];
    }

    public static function aTexto(int $minutos): string
    {
        return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
    }

    /** Recorta "15:00:00" a "15:00". */
    public static function corta(?string $hora): string
    {
        return $hora === null ? '' : substr($hora, 0, 5);
    }

    /** Dos rangos [inicio, fin) se enciman si cada uno empieza antes de que el otro termine. */
    public static function seEnciman(int $inicioA, int $finA, int $inicioB, int $finB): bool
    {
        return $inicioA < $finB && $inicioB < $finA;
    }

    /** "1 h 40 min", "50 min" */
    public static function duracion(int $minutos): string
    {
        $h = intdiv($minutos, 60);
        $m = $minutos % 60;

        return trim(($h ? "{$h} h " : '') . ($m ? "{$m} min" : ''));
    }
}
