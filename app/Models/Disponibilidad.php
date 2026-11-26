<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disponibilidad extends Model
{
    // Laravel pluraliza en inglés ("disponibilidads"), por eso se indica la tabla
    protected $table = 'disponibilidades';

    protected $fillable = ['docente_id', 'dia', 'franja_id'];

    protected function casts(): array
    {
        return ['dia' => 'integer'];
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    public function franja(): BelongsTo
    {
        return $this->belongsTo(Franja::class);
    }
}
