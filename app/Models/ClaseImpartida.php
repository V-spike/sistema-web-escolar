<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Cuántas clases dio el docente de una materia a un grupo en un parcial (sale en la boleta). */
class ClaseImpartida extends Model
{
    protected $table = 'clases_impartidas';   // Laravel buscaría "clase_impartidas"

    protected $fillable = ['semestre_id', 'grupo_id', 'materia_id', 'parcial', 'total'];

    protected function casts(): array
    {
        return ['parcial' => 'integer', 'total' => 'integer'];
    }
}
