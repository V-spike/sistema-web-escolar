<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aula extends Model
{
    /** Valores permitidos de tipo (los mismos que muestran las vistas) */
    public const TIPOS = ['salon', 'laboratorio', 'auditorio'];

    protected $fillable = ['clave', 'edificio', 'tipo', 'capacidad', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'capacidad' => 'integer'];
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class);
    }
}
