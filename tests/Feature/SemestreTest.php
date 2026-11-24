<?php

namespace Tests\Feature;

use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemestreTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    public function test_el_seeder_deja_un_semestre_activo(): void
    {
        $this->assertSame(1, Semestre::where('activo', true)->count());
        $this->assertSame('2026-2027 A', Semestre::activo()->nombre);
    }

    public function test_el_listado_y_los_formularios_abren(): void
    {
        $this->actingAs($this->admin())->get('/panel/semestres')->assertOk()->assertSee('2026-2027 A');
        $this->actingAs($this->admin())->get('/panel/semestres/create')->assertOk();
        $this->actingAs($this->admin())->get('/panel/semestres/' . Semestre::first()->id . '/edit')->assertOk();
    }

    public function test_la_fecha_de_fin_debe_ser_posterior(): void
    {
        $this->actingAs($this->admin())
            ->post('/panel/semestres', ['nombre' => '2026-2027 B', 'fecha_inicio' => '2027-02-01', 'fecha_fin' => '2027-01-01'])
            ->assertSessionHasErrors('fecha_fin');
    }

    public function test_el_nombre_no_se_repite(): void
    {
        $this->actingAs($this->admin())
            ->post('/panel/semestres', ['nombre' => '2026-2027 A', 'fecha_inicio' => '2027-02-01', 'fecha_fin' => '2027-07-01'])
            ->assertSessionHasErrors('nombre');
    }

    public function test_activar_deja_solo_uno_activo(): void
    {
        $this->actingAs($this->admin())
            ->post('/panel/semestres', ['nombre' => '2026-2027 B', 'fecha_inicio' => '2027-02-01', 'fecha_fin' => '2027-07-01'])
            ->assertRedirect('/panel/semestres');

        $nuevo = Semestre::where('nombre', '2026-2027 B')->first();
        $this->assertFalse($nuevo->activo);

        $this->actingAs($this->admin())->patch("/panel/semestres/{$nuevo->id}/activar")->assertRedirect('/panel/semestres');

        $this->assertSame(1, Semestre::where('activo', true)->count());
        $this->assertSame($nuevo->id, Semestre::activo()->id);
    }

    public function test_un_docente_no_entra(): void
    {
        $this->actingAs(User::where('rol', 'docente')->first())->get('/panel/semestres')->assertForbidden();
    }
}
