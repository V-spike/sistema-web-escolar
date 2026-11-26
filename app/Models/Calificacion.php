<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Calificación de un alumno en una materia y un parcial.
 * Materia normal: de 5 a 10 con un decimal, o NP (no presentó).
 * Materia acreditable (Currículum Ampliado): A (acreditado) o N/A (no acreditado).
 */
class Calificacion extends Model
{
    protected $table = 'calificaciones';   // Laravel buscaría "calificacions"

    protected $fillable = ['semestre_id', 'alumno_id', 'materia_id', 'parcial', 'valor', 'faltas'];

    protected function casts(): array
    {
        return ['parcial' => 'integer', 'faltas' => 'integer'];
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alumno_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    /**
     * Limpia lo que escribió el docente: "8,5" → "8.5", "np" → "NP", "n/a" → "N/A".
     * Devuelve null si no es válido para ese tipo de materia.
     */
    public static function normalizar(string $valor, Materia $materia): ?string
    {
        $valor = strtoupper(str_replace([',', ' '], ['.', ''], trim($valor)));

        if ($materia->tipo === 'acreditable') {
            return in_array($valor, ['A', 'N/A', 'NA'], true) ? ($valor === 'A' ? 'A' : 'N/A') : null;
        }

        if ($valor === 'NP') {
            return 'NP';
        }

        // Número de 5 a 10 con máximo un decimal
        if (! preg_match('/^\d{1,2}(\.\d)?$/', $valor) || (float) $valor < 5 || (float) $valor > 10) {
            return null;
        }

        return rtrim(rtrim(number_format((float) $valor, 1, '.', ''), '0'), '.');   // "8.0" → "8"
    }

    /** Valor numérico para promediar. NP cuenta como 5. A y N/A no se promedian. */
    public static function comoNumero(?string $valor): ?float
    {
        if ($valor === null || in_array($valor, ['A', 'N/A'], true)) {
            return null;
        }

        return $valor === 'NP' ? 5.0 : (float) $valor;
    }
}
