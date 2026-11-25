<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semestre extends Model
{
    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'activo'];

    protected function casts(): array
    {
        // Las fechas como date para poder usar ->format() e ->isPast() en las vistas
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'activo' => 'boolean',
        ];
    }

    /**
     * El periodo activo, o null si todavía no hay ninguno.
     * Uso: Semestre::activo()
     */
    public static function activo(): ?Semestre
    {
        return static::where('activo', true)->first();
    }

    public function grupos(): HasMany
    {
    return $this->hasMany(Grupo::class);
    }

}
