<?php

namespace App\Models;

use App\Horarios\ConRango;
use Illuminate\Database\Eloquent\Model;

class Receso extends Model
{
    use ConRango;

    protected $fillable = ['nombre', 'hora_inicio', 'hora_fin'];
}
