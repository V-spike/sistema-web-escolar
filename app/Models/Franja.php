<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Franja extends Model
{
    protected $fillable = ['orden', 'hora_inicio', 'hora_fin', 'es_receso'];

    protected function casts(): array
    {
        return ['orden' => 'integer', 'es_receso' => 'boolean'];
    }

    /**
     * Solo las franjas de clase (sin recesos), en orden.
     * Uso: Franja::clases()->get()
     */
    public function scopeClases($query)
    {
        return $query->where('es_receso', false)->orderBy('orden');
    }
}
