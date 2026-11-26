<?php

namespace Tests\Feature;

use App\Models\Docente;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocenteTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    public function test_sin_sesion_manda_al_login(): void
    {
        $this->get('/panel/docentes')->assertRedirect('/login');
    }

    public function test_un_docente_no_entra(): void
    {
        $docente = User::where('rol', 'docente')->first();

        $this->actingAs($docente)->get('/panel/docentes')->assertForbidden();
    }

    public function test_la_lista_muestra_los_28_docentes_del_seeder(): void
    {
        $this->assertSame(28, Docente::count());

        $this->actingAs($this->admin())
            ->get('/panel/docentes')
            ->assertOk()
            ->assertSee('D-001')
            ->assertSee('Acosta');
    }

    public function test_los_formularios_abren(): void
    {
        $docente = Docente::first();

        $this->actingAs($this->admin())->get('/panel/docentes/create')->assertOk();
        $this->actingAs($this->admin())->get("/panel/docentes/{$docente->id}/edit")->assertOk();
    }

    public function test_registrar_un_docente_con_su_perfil(): void
    {
        $materia = Materia::where('clave', 'PM1')->first();

        $this->actingAs($this->admin())
            ->post('/panel/docentes', [
                'numero_personal' => 'D-100',
                'nombre' => 'Ana',
                'apellido_paterno' => 'Prueba',
                'horas_plaza' => 20,
                'materias' => [$materia->id],
            ])
            ->assertRedirect('/panel/docentes');

        $docente = Docente::where('numero_personal', 'D-100')->first();
        $this->assertNotNull($docente);
        $this->assertTrue($docente->materias->contains($materia));
    }

    public function test_el_numero_de_personal_no_se_repite(): void
    {
        $this->actingAs($this->admin())
            ->post('/panel/docentes', [
                'numero_personal' => 'D-001',
                'nombre' => 'Otra',
                'apellido_paterno' => 'Persona',
                'horas_plaza' => 20,
            ])
            ->assertSessionHasErrors('numero_personal');
    }

    public function test_dar_de_baja_y_reactivar(): void
    {
        $docente = Docente::first();

        $this->actingAs($this->admin())->patch("/panel/docentes/{$docente->id}/estado");
        $this->assertFalse($docente->fresh()->activo);

        $this->actingAs($this->admin())->patch("/panel/docentes/{$docente->id}/estado");
        $this->assertTrue($docente->fresh()->activo);
    }
}
