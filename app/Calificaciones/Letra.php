<?php

namespace App\Calificaciones;

/**
 * Escribe una calificación con letra, como en la boleta: 8.5 → "OCHO PUNTO CINCO".
 */
class Letra
{
    private const NUMEROS = ['CERO', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ'];

    public static function de(?float $numero): string
    {
        if ($numero === null) {
            return '';
        }

        $entero = (int) floor($numero);
        $decimal = (int) round(($numero - $entero) * 10);

        $texto = self::NUMEROS[$entero] ?? (string) $entero;

        return $decimal > 0 ? "{$texto} PUNTO " . self::NUMEROS[$decimal] : $texto;
    }
}
