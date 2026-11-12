<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = ['clave', 'nombre', 'horas_semana', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'horas_semana' => 'integer'];
    }
}
