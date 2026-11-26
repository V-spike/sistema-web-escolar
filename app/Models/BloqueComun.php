<?php

namespace App\Models;

use App\Horarios\ConRango;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloqueComun extends Model
{
    use ConRango;

    // Laravel deduciría "bloque_comuns"
    protected $table = 'bloques_comunes';

    protected $fillable = ['semestre_id', 'grado', 'materia_id', 'dia', 'hora_inicio', 'hora_fin'];

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
}
