<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $fillable = ['clave', 'edificio', 'capacidad', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'capacidad' => 'integer'];
    }
}
