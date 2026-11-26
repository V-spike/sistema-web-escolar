<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Conducta del alumno en el semestre. La captura su tutor o la administración. */
class Conducta extends Model
{
    public const VALORES = ['buena' => 'Buena', 'regular' => 'Regular', 'mala' => 'Mala'];

    protected $fillable = ['semestre_id', 'alumno_id', 'valor'];
}
