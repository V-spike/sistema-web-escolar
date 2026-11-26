<?php

namespace Tests\Feature;

use App\Models\BloqueComun;
use App\Models\Franja;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Necesita la columna materias.tipo de T-40 (Marcos).
 */
class BloqueComunTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    private function materiaComun(): Materia
    {
        $materia = Materia::firstOrCreate(
            ['clave' => 'CA1'],
            ['nombre' => 'Currículum Ampliado', 'horas_semana' => 2, 'activo' => true]
        );
        $materia->forceFill(['tipo' => 'bloque_comun'])->save();

        return $materia;
    }

    public function test_agregar_un_bloque_comun(): void
    {
        $materia = $this->materiaComun();
        $franja = Franja::clases()->first();

        $this->actingAs($this->admin())
            ->post('/panel/bloques-comunes', [
                'grado' => 1, 'materia_id' => $materia->id, 'dia' => 1, 'franja_id' => $franja->id,
            ])
            ->assertRedirect('/panel/bloques-comunes');

        $this->assertDatabaseHas('bloques_comunes', [
            'semestre_id' => Semestre::activo()->id, 'grado' => 1, 'dia' => 1, 'franja_id' => $franja->id,
        ]);
    }

    public function test_no_acepta_un_receso(): void
    {
        $materia = $this->materiaComun();
        $receso = Franja::where('es_receso', true)->first();

        $this->actingAs($this->admin())
            ->post('/panel/bloques-comunes', [
                'grado' => 1, 'materia_id' => $materia->id, 'dia' => 1, 'franja_id' => $receso->id,
            ])
            ->assertSessionHasErrors('franja_id');
    }

    public function test_no_repite_grado_dia_y_franja(): void
    {
        $materia = $this->materiaComun();
        $franja = Franja::clases()->first();
        $datos = ['grado' => 2, 'materia_id' => $materia->id, 'dia' => 3, 'franja_id' => $franja->id];

        $this->actingAs($this->admin())->post('/panel/bloques-comunes', $datos);
        $this->actingAs($this->admin())
            ->post('/panel/bloques-comunes', $datos)
            ->assertSessionHasErrors('franja_id');

        $this->assertSame(1, BloqueComun::where('grado', 2)->count());
    }

    public function test_quitar_un_bloque(): void
    {
        $materia = $this->materiaComun();
        $bloque = BloqueComun::create([
            'semestre_id' => Semestre::activo()->id, 'grado' => 3, 'materia_id' => $materia->id,
            'dia' => 5, 'franja_id' => Franja::clases()->first()->id,
        ]);

        $this->actingAs($this->admin())
            ->delete("/panel/bloques-comunes/{$bloque->id}")
            ->assertRedirect('/panel/bloques-comunes');

        $this->assertModelMissing($bloque);
    }
}
