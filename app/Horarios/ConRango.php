<?php

namespace App\Horarios;

/**
 * Para modelos con hora_inicio y hora_fin (clases, recesos, disponibilidad, bloques comunes).
 */
trait ConRango
{
    public function getInicioAttribute(): int
    {
        return Hora::aMinutos($this->hora_inicio);
    }

    public function getFinAttribute(): int
    {
        return Hora::aMinutos($this->hora_fin);
    }

    public function getMinutosAttribute(): int
    {
        return $this->fin - $this->inicio;
    }

    public function getRangoAttribute(): string
    {
        return Hora::corta($this->hora_inicio) . '–' . Hora::corta($this->hora_fin);
    }

    public function seEncimaCon(int $inicio, int $fin): bool
    {
        return Hora::seEnciman($this->inicio, $this->fin, $inicio, $fin);
    }
}
