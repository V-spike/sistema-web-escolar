<?php

namespace Tests\Feature;

use App\Models\Franja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JornadaTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    public function test_la_jornada_tiene_7_clases_y_2_recesos(): void
    {
        $this->assertSame(9, Franja::count());
        $this->assertSame(7, Franja::clases()->count());
        $this->assertSame(2, Franja::where('es_receso', true)->count());
    }

    public function test_los_dias_vienen_de_config(): void
    {
        $this->assertSame('Lunes', config('escolar.dias')[1]);
        $this->assertCount(5, config('escolar.dias'));
    }

    public function test_la_hora_de_fin_debe_ser_posterior(): void
    {
        $franja = Franja::where('orden', 1)->first();

        $this->actingAs($this->admin())
            ->put("/panel/jornada/{$franja->id}", ['hora_inicio' => '15:50', 'hora_fin' => '15:00'])
            ->assertSessionHasErrors('hora_fin');
    }

    public function test_actualizar_una_franja(): void
    {
        $franja = Franja::where('orden', 4)->first(); // receso

        $this->actingAs($this->admin())
            ->put("/panel/jornada/{$franja->id}", ['hora_inicio' => '17:30', 'hora_fin' => '17:55'])
            ->assertRedirect('/panel/jornada');

        // Sin la casilla marcada, es_receso queda en false
        $this->assertFalse($franja->fresh()->es_receso);
    }
}
