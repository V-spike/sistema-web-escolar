<?php

namespace Tests\Feature;

use App\Models\Clase;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Jornada;
use App\Models\Materia;
use App\Models\Receso;
use App\Models\Semestre;
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

    private function claseDePrueba(string $inicio, string $fin): Clase
    {
        $grupo = Grupo::where('letra', 'A')->first();
        $materia = Materia::where('clave', 'PM1')->first();

        return Clase::create([
            'semestre_id' => Semestre::activo()->id, 'grupo_id' => $grupo->id, 'materia_id' => $materia->id,
            'docente_id' => $materia->docentes()->first()->id, 'aula_id' => $grupo->aula_id,
            'dia' => 2, 'hora_inicio' => $inicio, 'hora_fin' => $fin,
        ]);
    }

    public function test_la_jornada_del_seeder(): void
    {
        $jornada = Jornada::actual();
        $this->assertSame('15:00', $jornada->hora_entrada_corta);
        $this->assertSame('21:20', $jornada->hora_salida_corta);
        $this->assertSame(2, Receso::count());
    }

    public function test_la_pantalla_abre(): void
    {
        $this->actingAs($this->admin())->get('/panel/jornada')->assertOk()->assertSee('Receso 1');
    }

    public function test_la_salida_debe_ser_posterior(): void
    {
        $this->actingAs($this->admin())
            ->put('/panel/jornada', ['hora_entrada' => '15:00', 'hora_salida' => '14:00', 'minutos_por_hora' => 50])
            ->assertSessionHasErrors('hora_salida');
    }

    public function test_no_deja_clases_fuera_de_la_jornada(): void
    {
        $this->claseDePrueba('20:30', '21:20');

        $this->actingAs($this->admin())
            ->put('/panel/jornada', ['hora_entrada' => '15:00', 'hora_salida' => '21:00', 'minutos_por_hora' => 50])
            ->assertSessionHasErrors('hora_salida');

        $this->assertSame('21:20', Jornada::actual()->hora_salida_corta);
    }

    public function test_agregar_un_receso(): void
    {
        $this->actingAs($this->admin())
            ->post('/panel/jornada/recesos', ['nombre' => 'Receso 3', 'hora_inicio' => '20:20', 'hora_fin' => '20:30'])
            ->assertRedirect('/panel/jornada');

        $this->assertSame(3, Receso::count());
    }

    public function test_un_receso_no_se_encima_con_otro_ni_con_clases(): void
    {
        $this->actingAs($this->admin())
            ->post('/panel/jornada/recesos', ['nombre' => 'Encimado', 'hora_inicio' => '17:40', 'hora_fin' => '18:00'])
            ->assertSessionHasErrors('hora_inicio');

        $this->claseDePrueba('15:00', '15:50');
        $this->actingAs($this->admin())
            ->post('/panel/jornada/recesos', ['nombre' => 'Sobre clase', 'hora_inicio' => '15:30', 'hora_fin' => '15:40'])
            ->assertSessionHasErrors('hora_inicio');

        $this->assertSame(2, Receso::count());
    }
}
