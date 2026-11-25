<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Materia extends Model
{
    protected $fillable = ['clave', 'nombre', 'horas_semana', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'horas_semana' => 'integer'];
    }

    public function docentes(): BelongsToMany
    {
    return $this->belongsToMany(Docente::class);
    }

}
