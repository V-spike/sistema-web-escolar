<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grupo extends Model
{
    protected $fillable = [
        'semestre_id',
        'grado',
        'letra',
        'aula_id',
        'capacidad',
        'area',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'grado' => 'integer',
            'capacidad' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class);
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }
}