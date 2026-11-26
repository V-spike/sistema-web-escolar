<?php

namespace Tests\Feature;

use App\Models\Clase;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisponibilidadTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    private function docente(): Docente
    {
        return Docente::create(['numero_personal' => 'D-999', 'nombre' => 'Ana', 'apellido_paterno' => 'Prueba', 'horas_plaza' => 20]);
    }

    public function test_sin_sesion_manda_al_login(): void
    {
        $this->get('/panel/docentes/1/disponibilidad')->assertRedirect('/login');
    }

    public function test_se_ve_la_pantalla(): void
    {
        $docente = $this->docente();

        $this->actingAs($this->admin())
            ->get("/panel/docentes/{$docente->id}/disponibilidad")
            ->assertOk()
            ->assertSee('Disponibilidad de Ana');
    }

    public function test_agregar_un_rango_en_varios_dias(): void
    {
        $docente = $this->docente();

        $this->actingAs($this->admin())
            ->post("/panel/docentes/{$docente->id}/disponibilidad", ['dias' => [1, 3], 'hora_inicio' => '15:00', 'hora_fin' => '18:00'])
            ->assertRedirect("/panel/docentes/{$docente->id}/disponibilidad");

        $this->assertSame(2, $docente->disponibilidades()->count());
    }

    public function test_rangos_que_se_enciman_se_juntan(): void
    {
        $docente = $this->docente();
        $this->actingAs($this->admin())->post("/panel/docentes/{$docente->id}/disponibilidad", ['dias' => [1], 'hora_inicio' => '15:00', 'hora_fin' => '17:00']);
        $this->actingAs($this->admin())->post("/panel/docentes/{$docente->id}/disponibilidad", ['dias' => [1], 'hora_inicio' => '16:30', 'hora_fin' => '19:00']);

        $rangos = $docente->disponibilidades()->get();
        $this->assertCount(1, $rangos);
        $this->assertSame('15:00–19:00', $rangos->first()->rango);
    }

    public function test_no_acepta_rangos_fuera_de_la_jornada_ni_al_reves(): void
    {
        $docente = $this->docente();

        $this->actingAs($this->admin())
            ->post("/panel/docentes/{$docente->id}/disponibilidad", ['dias' => [1], 'hora_inicio' => '13:00', 'hora_fin' => '16:00'])
            ->assertSessionHasErrors('hora_inicio');
        $this->actingAs($this->admin())
            ->post("/panel/docentes/{$docente->id}/disponibilidad", ['dias' => [1], 'hora_inicio' => '17:00', 'hora_fin' => '16:00'])
            ->assertSessionHasErrors('hora_fin');
        $this->actingAs($this->admin())
            ->post("/panel/docentes/{$docente->id}/disponibilidad", ['dias' => [9], 'hora_inicio' => '15:00', 'hora_fin' => '16:00'])
            ->assertSessionHasErrors('dias.0');

        $this->assertSame(0, $docente->disponibilidades()->count());
    }

    public function test_no_se_quita_un_rango_con_clases_dentro(): void
    {
        $docente = Docente::where('numero_personal', 'D-005')->first(); // ING1
        $grupo = Grupo::where('letra', 'A')->first();
        $rango = $docente->disponibilidades()->where('dia', 1)->first();

        Clase::create([
            'semestre_id' => Semestre::activo()->id, 'grupo_id' => $grupo->id,
            'materia_id' => $docente->materias()->first()->id, 'docente_id' => $docente->id,
            'aula_id' => $grupo->aula_id, 'dia' => 1, 'hora_inicio' => '15:00', 'hora_fin' => '15:50',
        ]);

        $this->actingAs($this->admin())
            ->delete("/panel/docentes/{$docente->id}/disponibilidad/{$rango->id}")
            ->assertSessionHasErrors('rango');

        $this->assertNotNull($rango->fresh());
    }

    public function test_disponible_toda_la_jornada(): void
    {
        $docente = $this->docente();

        $this->actingAs($this->admin())->post("/panel/docentes/{$docente->id}/disponibilidad/completa");

        $this->assertSame(5, $docente->disponibilidades()->count());
    }
}
