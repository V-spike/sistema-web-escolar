<?php

namespace App\Models;

use App\Horarios\ConRango;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disponibilidad extends Model
{
    use ConRango;

    // Laravel pluraliza en inglés ("disponibilidads"), por eso se indica la tabla
    protected $table = 'disponibilidades';

    protected $fillable = ['docente_id', 'dia', 'hora_inicio', 'hora_fin'];

    protected function casts(): array
    {
        return ['dia' => 'integer'];
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }
}
