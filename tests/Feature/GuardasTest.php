<?php

namespace Tests\Feature;

use App\Models\Aula;
use App\Models\Clase;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cambios en catálogos que romperían un horario ya acomodado: se rechazan con su causa.
 */
class GuardasTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private Clase $clase;

    protected function setUp(): void
    {
        parent::setUp();

        $grupo = Grupo::where('letra', 'A')->first();
        $this->clase = Clase::create([
            'semestre_id' => Semestre::activo()->id, 'grupo_id' => $grupo->id,
            'materia_id' => Materia::where('clave', 'PM1')->first()->id,
            'docente_id' => Docente::where('numero_personal', 'D-008')->first()->id,
            'aula_id' => $grupo->aula_id, 'dia' => 1, 'hora_inicio' => '15:00', 'hora_fin' => '15:50',
        ]);
        $this->actingAs(User::where('rol', 'administrador')->first());
    }

    public function test_no_se_da_de_baja_un_docente_con_clases(): void
    {
        $this->patch("/panel/docentes/{$this->clase->docente_id}/estado")->assertSessionHasErrors('estado');
        $this->assertTrue($this->clase->docente->fresh()->activo);
    }

    public function test_no_se_quita_del_perfil_una_materia_que_ya_da(): void
    {
        $docente = $this->clase->docente;

        $this->put("/panel/docentes/{$docente->id}", [
            'numero_personal' => $docente->numero_personal, 'nombre' => $docente->nombre,
            'apellido_paterno' => $docente->apellido_paterno, 'horas_plaza' => $docente->horas_plaza, 'materias' => [],
        ])->assertSessionHasErrors('materias');

        $this->assertTrue($docente->materias()->whereKey($this->clase->materia_id)->exists());
    }

    public function test_no_se_da_de_baja_un_grupo_un_aula_ni_una_materia_en_uso(): void
    {
        $this->patch("/panel/grupos/{$this->clase->grupo_id}/estado")->assertSessionHasErrors('estado');
        $this->patch("/panel/aulas/{$this->clase->aula_id}/estado")->assertSessionHasErrors('estado');
        $this->patch("/panel/materias/{$this->clase->materia_id}/estado")->assertSessionHasErrors('estado');

        $this->assertTrue(Grupo::find($this->clase->grupo_id)->activo);
        $this->assertTrue(Aula::find($this->clase->aula_id)->activo);
        $this->assertTrue(Materia::find($this->clase->materia_id)->activo);
    }

    public function test_no_se_cambia_el_grado_de_un_grupo_con_clases(): void
    {
        $grupo = $this->clase->grupo;

        $this->put("/panel/grupos/{$grupo->id}", [
            'semestre_id' => $grupo->semestre_id, 'grado' => 3, 'letra' => 'A', 'capacidad' => 50,
        ])->assertSessionHasErrors('grado');

        $this->assertSame(1, $grupo->fresh()->grado);
    }

    public function test_un_docente_sin_clases_si_se_da_de_baja(): void
    {
        $libre = Docente::where('numero_personal', 'D-002')->first();

        $this->patch("/panel/docentes/{$libre->id}/estado")->assertSessionHasNoErrors();
        $this->assertFalse($libre->fresh()->activo);
    }
}
