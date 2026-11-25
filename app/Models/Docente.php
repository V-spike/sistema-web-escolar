<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Docente extends Model
{
    protected $fillable = [
        'user_id',
        'numero_personal',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'curp',
        'rfc',
        'telefono',
        'correo',
        'categoria',
        'sindicato',
        'titulo',
        'cedula',
        'horas_plaza',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'horas_plaza' => 'integer',
        ];
    }

    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(Materia::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}