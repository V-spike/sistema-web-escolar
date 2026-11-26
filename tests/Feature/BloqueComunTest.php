<?php

namespace Tests\Feature;

use App\Models\BloqueComun;
use App\Models\Clase;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BloqueComunTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    private function datos(array $cambios = []): array
    {
        return $cambios + [
            'grado' => 1,
            'materia_id' => Materia::where('clave', 'CA1')->first()->id,
            'dia' => 3,
            'hora_inicio' => '15:00',
            'hora_fin' => '15:50',
        ];
    }

    public function test_el_seeder_deja_el_curriculum_ampliado(): void
    {
        $bloque = BloqueComun::first();
        $this->assertSame(1, $bloque->dia);
        $this->assertSame('17:50–19:30', $bloque->rango);
    }

    public function test_agregar_un_bloque_comun(): void
    {
        $this->actingAs($this->admin())->post('/panel/bloques-comunes', $this->datos())->assertRedirect('/panel/bloques-comunes');

        $this->assertSame(2, BloqueComun::count());
    }

    public function test_no_acepta_un_receso_ni_otro_bloque_encima(): void
    {
        $this->actingAs($this->admin())
            ->post('/panel/bloques-comunes', $this->datos(['hora_inicio' => '17:20', 'hora_fin' => '17:40']))
            ->assertSessionHasErrors('hora_inicio');

        $this->actingAs($this->admin())
            ->post('/panel/bloques-comunes', $this->datos(['dia' => 1, 'hora_inicio' => '19:00', 'hora_fin' => '19:20']))
            ->assertSessionHasErrors('hora_inicio');

        $this->assertSame(1, BloqueComun::count());
    }

    public function test_no_acepta_materias_que_no_son_bloque_comun(): void
    {
        $this->actingAs($this->admin())
            ->post('/panel/bloques-comunes', $this->datos(['materia_id' => Materia::where('clave', 'PM1')->first()->id]))
            ->assertSessionHasErrors('materia_id');
    }

    public function test_no_se_pone_encima_de_clases_del_grado(): void
    {
        $grupo = Grupo::where('letra', 'B')->first();
        $materia = Materia::where('clave', 'PM1')->first();
        Clase::create([
            'semestre_id' => Semestre::activo()->id, 'grupo_id' => $grupo->id, 'materia_id' => $materia->id,
            'docente_id' => $materia->docentes()->first()->id, 'aula_id' => $grupo->aula_id,
            'dia' => 3, 'hora_inicio' => '15:00', 'hora_fin' => '15:50',
        ]);

        $this->actingAs($this->admin())
            ->post('/panel/bloques-comunes', $this->datos())
            ->assertSessionHasErrors('hora_inicio');
    }

    public function test_quitar_un_bloque(): void
    {
        $bloque = BloqueComun::first();

        $this->actingAs($this->admin())->delete("/panel/bloques-comunes/{$bloque->id}");

        $this->assertNull($bloque->fresh());
    }
}
