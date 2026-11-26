<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function disponibilidades(): HasMany
    {
        return $this->hasMany(Disponibilidad::class);
    }

    public function clases(): HasMany
    {
        return $this->hasMany(Clase::class);
    }

    /** "Acosta Rebolledo Isaí Rubén" */
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->apellido_paterno} {$this->apellido_materno} {$this->nombre}");
    }
}
