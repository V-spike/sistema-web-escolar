<?php

namespace Tests\Feature;

use App\Models\Grupo;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrupoTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    public function test_sin_sesion_manda_al_login(): void
    {
        $this->get('/panel/grupos')->assertRedirect('/login');
    }

    public function test_un_alumno_no_entra(): void
    {
        $alumno = User::where('rol', 'alumno')->where('activo', true)->first();

        $this->actingAs($alumno)->get('/panel/grupos')->assertForbidden();
    }

    public function test_la_lista_muestra_los_7_grupos_de_primero(): void
    {
        $this->assertSame(7, Grupo::where('grado', 1)->count());

        $this->actingAs($this->admin())
            ->get('/panel/grupos')
            ->assertOk()
            ->assertSee('1.º A')
            ->assertSee('1.º G')
            ->assertSee('S-1');
    }

    public function test_los_formularios_abren(): void
    {
        $grupo = Grupo::first();

        $this->actingAs($this->admin())->get('/panel/grupos/create')->assertOk();
        $this->actingAs($this->admin())->get("/panel/grupos/{$grupo->id}/edit")->assertOk();
    }

    public function test_registrar_un_grupo(): void
    {
        $semestre = Semestre::where('activo', true)->first();

        $this->actingAs($this->admin())
            ->post('/panel/grupos', ['semestre_id' => $semestre->id, 'grado' => 2, 'letra' => 'a', 'capacidad' => 40])
            ->assertRedirect('/panel/grupos');

        $this->assertDatabaseHas('grupos', ['grado' => 2, 'letra' => 'A']);
    }

    public function test_no_se_repite_el_grupo_en_el_mismo_semestre(): void
    {
        $semestre = Semestre::where('activo', true)->first();

        $this->actingAs($this->admin())
            ->post('/panel/grupos', ['semestre_id' => $semestre->id, 'grado' => 1, 'letra' => 'A', 'capacidad' => 40])
            ->assertSessionHasErrors('letra');
    }

    public function test_dar_de_baja_y_reactivar(): void
    {
        $grupo = Grupo::where('letra', 'G')->first();   // 1.º G no tiene alumnos ni clases

        $this->actingAs($this->admin())->patch("/panel/grupos/{$grupo->id}/estado");
        $this->assertFalse($grupo->fresh()->activo);

        $this->actingAs($this->admin())->patch("/panel/grupos/{$grupo->id}/estado");
        $this->assertTrue($grupo->fresh()->activo);
    }
}
