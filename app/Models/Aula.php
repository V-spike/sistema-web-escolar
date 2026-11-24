<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    /** Valores permitidos de tipo (los mismos que muestran las vistas) */
    public const TIPOS = ['salon', 'laboratorio', 'auditorio'];

    protected $fillable = ['clave', 'edificio', 'tipo', 'capacidad', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'capacidad' => 'integer'];
    }
}
