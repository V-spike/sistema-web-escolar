<?php

namespace App\Horarios;

/**
 * Resultado de revisar una clase: los empalmes (impiden guardar) y los avisos (se puede guardar).
 * Cada problema lleva un código corto y la causa escrita para la persona.
 */
class Revision
{
    /** @var array<int, array{codigo: string, causa: string}> */
    public array $empalmes = [];

    /** @var array<int, array{codigo: string, causa: string}> */
    public array $avisos = [];

    public function empalme(string $codigo, string $causa): void
    {
        $this->empalmes[] = ['codigo' => $codigo, 'causa' => $causa];
    }

    public function aviso(string $codigo, string $causa): void
    {
        $this->avisos[] = ['codigo' => $codigo, 'causa' => $causa];
    }

    public function sePuedeGuardar(): bool
    {
        return $this->empalmes === [];
    }

    public function codigos(): array
    {
        return array_column($this->empalmes, 'codigo');
    }

    public function toArray(): array
    {
        return [
            'se_puede_guardar' => $this->sePuedeGuardar(),
            'empalmes' => $this->empalmes,
            'avisos' => $this->avisos,
        ];
    }
}
