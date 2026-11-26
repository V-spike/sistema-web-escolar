<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Materia extends Model
{
    /**
     * Tipos de materia. Los que llevan calificación en la boleta son "normal" (de 5 a 10)
     * y "bloque_comun" (A o N/A, como Currículum Ampliado).
     */
    public const TIPOS = [
        'normal' => 'Normal',
        'bloque_comun' => 'Bloque común',
        'extendido' => 'Formación fundamental extendida',
        'tutoria' => 'Tutoría',
    ];

    /** Tipos que aparecen en la boleta */
    public const EN_BOLETA = ['normal', 'bloque_comun'];

    protected $fillable = [
    'clave',
    'clave_corta',
    'nombre',
    'semestre',
    'horas_semana',
    'tipo',
    'activo',
    ];

    protected function casts(): array
    {
        return [
        'activo' => 'boolean',
        'semestre' => 'integer',
        'horas_semana' => 'integer',
        ];
    }

    public function docentes(): BelongsToMany
    {
    return $this->belongsToMany(Docente::class);
    }

}
