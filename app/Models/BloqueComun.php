<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloqueComun extends Model
{
    // Laravel deduciría "bloque_comuns"
    protected $table = 'bloques_comunes';

    protected $fillable = ['semestre_id', 'grado', 'materia_id', 'dia', 'franja_id'];

    protected function casts(): array
    {
        return ['grado' => 'integer', 'dia' => 'integer'];
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function franja(): BelongsTo
    {
        return $this->belongsTo(Franja::class);
    }
}
