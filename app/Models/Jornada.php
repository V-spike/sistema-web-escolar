<?php

namespace App\Models;

use App\Horarios\Hora;
use Illuminate\Database\Eloquent\Model;

/**
 * Horario del turno: hora de entrada, hora de salida y cuántos minutos dura una "hora clase".
 * Hay una sola fila; se obtiene con Jornada::actual().
 */
class Jornada extends Model
{
    protected $fillable = ['hora_entrada', 'hora_salida', 'minutos_por_hora'];

    protected function casts(): array
    {
        return ['minutos_por_hora' => 'integer'];
    }

    public static function actual(): self
    {
        return static::query()->first()
            ?? new static(['hora_entrada' => '15:00', 'hora_salida' => '21:20', 'minutos_por_hora' => 50]);
    }

    public function getEntradaAttribute(): int
    {
        return Hora::aMinutos($this->hora_entrada);
    }

    public function getSalidaAttribute(): int
    {
        return Hora::aMinutos($this->hora_salida);
    }

    public function getHoraEntradaCortaAttribute(): string
    {
        return Hora::corta($this->hora_entrada);
    }

    public function getHoraSalidaCortaAttribute(): string
    {
        return Hora::corta($this->hora_salida);
    }
}
