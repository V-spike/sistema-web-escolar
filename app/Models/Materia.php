<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
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
}
