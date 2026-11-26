<?php

namespace App\Models;

use App\Horarios\ConRango;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Una clase del horario: un grupo recibe una materia con un docente, en un aula,
 * un día de la semana, de hora_inicio a hora_fin.
 */
class Clase extends Model
{
    use ConRango;

    protected $fillable = [
        'semestre_id', 'grupo_id', 'materia_id', 'docente_id', 'aula_id', 'dia', 'hora_inicio', 'hora_fin',
    ];

    protected function casts(): array
    {
        return ['dia' => 'integer'];
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(Semestre::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }
}
