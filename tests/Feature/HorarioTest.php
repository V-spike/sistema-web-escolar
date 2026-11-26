<?php

namespace Tests\Feature;

use App\Horarios\RevisorEmpalmes;
use App\Models\Aula;
use App\Models\Clase;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Receso;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Acomodar el horario a mano: cada empalme se rechaza y se explica su causa.
 */
class HorarioTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    private function grupo(string $letra = 'A'): Grupo
    {
        return Grupo::where('grado', 1)->where('letra', $letra)->first();
    }

    private function docente(string $numero): Docente
    {
        return Docente::where('numero_personal', $numero)->first();
    }

    private function materia(string $clave): Materia
    {
        return Materia::where('clave', $clave)->first();
    }

    /** Pensamiento Matemático para 1.º A con Breling (D-008), lunes 15:00–15:50 en S-1 */
    private function datos(array $cambios = []): array
    {
        return $cambios + [
            'grupo_id' => $this->grupo()->id,
            'materia_id' => $this->materia('PM1')->id,
            'docente_id' => $this->docente('D-008')->id,
            'aula_id' => $this->grupo()->aula_id,
            'dia' => 1,
            'hora_inicio' => '15:00',
            'hora_fin' => '15:50',
        ];
    }

    private function guardar(array $cambios = [])
    {
        return $this->actingAs($this->admin())->post('/panel/horarios', $this->datos($cambios));
    }

    /** Guarda y comprueba que se rechazó con ese código de empalme */
    private function rechazada(array $cambios, string $codigo, string $textoCausa): void
    {
        $antes = Clase::count();
        $respuesta = $this->guardar($cambios);

        $respuesta->assertSessionHas('empalmes');
        $empalmes = collect(session('empalmes'));
        $this->assertContains($codigo, $empalmes->pluck('codigo'), 'Causas: ' . $empalmes->pluck('causa')->join(' | '));
        $this->assertStringContainsString($textoCausa, $empalmes->firstWhere('codigo', $codigo)['causa']);
        $this->assertSame($antes, Clase::count(), 'La clase no debió guardarse');
    }

    // ---------- Acceso ----------

    public function test_sin_sesion_manda_al_login(): void
    {
        $this->get('/panel/horarios')->assertRedirect('/login');
    }

    public function test_un_docente_no_puede_acomodar(): void
    {
        $this->actingAs(User::where('rol', 'docente')->first())->get('/panel/horarios')->assertForbidden();
        $this->actingAs(User::where('rol', 'docente')->first())->post('/panel/horarios', $this->datos())->assertForbidden();
    }

    public function test_las_tres_vistas_abren(): void
    {
        $this->guardar();

        $this->actingAs($this->admin())->get('/panel/horarios?vista=grupo')->assertOk()->assertSee('Agregar clase a 1.º A')->assertSee('P.M1');
        $this->actingAs($this->admin())->get('/panel/horarios?vista=docente&id=' . $this->docente('D-008')->id)->assertOk()->assertSee('P.M1');
        $this->actingAs($this->admin())->get('/panel/horarios?vista=aula&id=' . $this->grupo()->aula_id)->assertOk()->assertSee('P.M1');
    }

    // ---------- Lo que sí se guarda ----------

    public function test_agregar_una_clase_sin_empalmes(): void
    {
        $this->guardar()
            ->assertRedirect('/panel/horarios?vista=grupo&id=' . $this->grupo()->id)
            ->assertSessionHas('estado', 'Clase agregada sin empalmes.');

        $this->assertDatabaseHas('clases', ['grupo_id' => $this->grupo()->id, 'dia' => 1, 'hora_inicio' => '15:00']);
    }

    public function test_hora_libre_y_clases_que_solo_se_tocan(): void
    {
        $this->guardar(['hora_inicio' => '15:05', 'hora_fin' => '15:55'])->assertSessionHasNoErrors()->assertSessionMissing('empalmes');
        // Empieza justo cuando termina la otra: no es empalme
        $this->guardar(['hora_inicio' => '15:55', 'hora_fin' => '16:45', 'materia_id' => $this->materia('ING1')->id, 'docente_id' => $this->docente('D-005')->id])
            ->assertSessionMissing('empalmes');

        $this->assertSame(2, Clase::count());
    }

    // ---------- Empalmes: cada uno con su causa ----------

    public function test_el_grupo_ya_tiene_clase(): void
    {
        $this->guardar();
        $this->rechazada([
            'materia_id' => $this->materia('ING1')->id, 'docente_id' => $this->docente('D-005')->id,
            'aula_id' => null, 'hora_inicio' => '15:30', 'hora_fin' => '16:20',
        ], 'grupo_ocupado', 'El grupo 1.º A ya tiene Pensamiento Matemático I de 15:00–15:50');
    }

    public function test_el_docente_ya_da_clase_a_otro_grupo(): void
    {
        $this->guardar();
        $this->rechazada([
            'grupo_id' => $this->grupo('B')->id, 'aula_id' => $this->grupo('B')->aula_id, 'hora_inicio' => '15:40', 'hora_fin' => '16:30',
        ], 'docente_ocupado', 'ya da Pensamiento Matemático I de 15:00–15:50 al grupo 1.º A');
    }

    public function test_el_aula_esta_ocupada(): void
    {
        $this->guardar();
        $this->rechazada([
            'grupo_id' => $this->grupo('B')->id, 'materia_id' => $this->materia('ING1')->id, 'docente_id' => $this->docente('D-005')->id,
        ], 'aula_ocupada', 'El aula S-1 ya está ocupada por el grupo 1.º A');
    }

    public function test_se_encima_con_un_receso(): void
    {
        $this->rechazada(['hora_inicio' => '17:00', 'hora_fin' => '17:50'], 'receso', 'Receso 1 (17:30–17:50)');
    }

    public function test_fuera_de_la_jornada(): void
    {
        $this->rechazada(['hora_inicio' => '14:30', 'hora_fin' => '15:20'], 'jornada', 'fuera de la jornada (15:00–21:20)');
        $this->rechazada(['hora_inicio' => '20:50', 'hora_fin' => '21:40'], 'jornada', 'fuera de la jornada');
    }

    public function test_se_encima_con_el_bloque_comun(): void
    {
        $this->rechazada(['hora_inicio' => '18:00', 'hora_fin' => '18:50'], 'bloque_comun', 'Todo 1.º tiene Currículum Ampliado (clubes) (bloque común) de 17:50–19:30');
    }

    public function test_el_docente_no_tiene_la_materia_en_su_perfil(): void
    {
        $this->rechazada(['docente_id' => $this->docente('D-005')->id], 'perfil', 'no tiene Pensamiento Matemático I en su perfil');
    }

    public function test_el_docente_no_esta_disponible(): void
    {
        // D-009 (Laboratorio de Investigación) solo puede martes y jueves de 15:00 a 19:30
        $this->rechazada([
            'materia_id' => $this->materia('LDI1')->id, 'docente_id' => $this->docente('D-009')->id,
        ], 'disponibilidad', 'no tiene disponibilidad ese día');

        $this->rechazada([
            'materia_id' => $this->materia('LDI1')->id, 'docente_id' => $this->docente('D-009')->id,
            'dia' => 2, 'hora_inicio' => '19:40', 'hora_fin' => '20:30',
        ], 'disponibilidad', '15:00–19:30');
    }

    public function test_el_grupo_ya_completo_las_horas_de_la_materia(): void
    {
        // Ciencias Sociales I lleva 2 h a la semana
        $cs = ['materia_id' => $this->materia('CS1')->id, 'docente_id' => $this->docente('D-013')->id];
        $this->guardar($cs + ['dia' => 2])->assertSessionMissing('empalmes');
        $this->guardar($cs + ['dia' => 3])->assertSessionMissing('empalmes');

        $this->rechazada($cs + ['dia' => 4], 'horas_materia', 'lleva 2 h a la semana');
    }

    public function test_el_docente_ya_completo_su_plaza(): void
    {
        $docente = $this->docente('D-008');
        $docente->update(['horas_plaza' => 1]);
        $this->guardar()->assertSessionMissing('empalmes');

        $this->rechazada(['dia' => 2], 'horas_plaza', 'tiene plaza de 1 h');
    }

    public function test_horas_al_reves(): void
    {
        $this->rechazada(['hora_inicio' => '16:00', 'hora_fin' => '15:00'], 'horas', 'posterior');
    }

    public function test_materia_de_otro_grado_o_bloque_comun(): void
    {
        $tercero = Materia::create(['clave' => 'X3', 'clave_corta' => 'X3', 'nombre' => 'Materia de tercero', 'semestre' => 3, 'horas_semana' => 3, 'tipo' => 'normal']);
        $this->docente('D-008')->materias()->attach($tercero->id);
        $this->rechazada(['materia_id' => $tercero->id], 'materia_grado', 'es de 3.º semestre');

        $this->rechazada(['materia_id' => $this->materia('CA1')->id], 'materia_bloque', 'es un bloque común');
    }

    public function test_algo_dado_de_baja(): void
    {
        $this->docente('D-008')->update(['activo' => false]);
        $this->rechazada([], 'docente_baja', 'está dado de baja');
    }

    public function test_formato_de_hora_invalido(): void
    {
        $this->guardar(['hora_inicio' => 'mañana'])->assertSessionHasErrors('hora_inicio');
        $this->assertSame(0, Clase::count());
    }

    // ---------- Avisos (sí se guarda) ----------

    public function test_avisos_que_no_impiden_guardar(): void
    {
        $chica = Aula::create(['clave' => 'CH-1', 'edificio' => 'Anexo', 'tipo' => 'salon', 'capacidad' => 10]);

        $this->guardar(['aula_id' => $chica->id])->assertSessionMissing('empalmes')->assertSessionHas('avisos');
        $this->assertStringContainsString('es para 10 y el grupo es de 50', session('avisos')[0]['causa']);
        $this->assertSame(1, Clase::count());
    }

    // ---------- Editar, mover y quitar ----------

    public function test_editar_no_choca_consigo_misma_y_un_cambio_con_empalme_no_se_aplica(): void
    {
        $this->guardar();
        $clase = Clase::first();

        $this->actingAs($this->admin())->get("/panel/horarios/{$clase->id}/editar")->assertOk();

        // Moverla 10 minutos: se encima con su lugar anterior, pero es la misma clase
        $this->actingAs($this->admin())
            ->put("/panel/horarios/{$clase->id}", $this->datos(['hora_inicio' => '15:10', 'hora_fin' => '16:00']))
            ->assertSessionMissing('empalmes');
        $this->assertSame('15:10', substr($clase->fresh()->hora_inicio, 0, 5));

        // Moverla encima del receso: se rechaza y se queda donde estaba
        $this->actingAs($this->admin())
            ->put("/panel/horarios/{$clase->id}", $this->datos(['hora_inicio' => '17:20', 'hora_fin' => '18:10']))
            ->assertSessionHas('empalmes');
        $this->assertSame('15:10', substr($clase->fresh()->hora_inicio, 0, 5));
    }

    public function test_quitar_una_clase(): void
    {
        $this->guardar();
        $clase = Clase::first();

        $this->actingAs($this->admin())->delete("/panel/horarios/{$clase->id}");

        $this->assertSame(0, Clase::count());
    }

    // ---------- Revisión en vivo y revisión de todo el semestre ----------

    public function test_revision_en_vivo_explica_la_causa_sin_guardar(): void
    {
        $this->guardar();

        $this->actingAs($this->admin())
            ->postJson('/panel/horarios/revisar', $this->datos(['grupo_id' => $this->grupo('B')->id, 'aula_id' => null]))
            ->assertOk()
            ->assertJsonPath('se_puede_guardar', false)
            ->assertJsonFragment(['codigo' => 'docente_ocupado']);

        $this->assertSame(1, Clase::count());
    }

    public function test_detecta_empalmes_que_aparecen_despues(): void
    {
        $this->guardar();
        // Alguien mete un receso directo en la base, encima de la clase
        Receso::create(['nombre' => 'Receso extra', 'hora_inicio' => '15:20', 'hora_fin' => '15:30']);

        $problemas = (new RevisorEmpalmes())->revisarSemestre(Semestre::activo());
        $this->assertCount(1, $problemas);

        $this->actingAs($this->admin())->get('/panel/horarios')->assertSee('Hay 1 clase con empalme');
        $this->actingAs($this->admin())->get('/panel/administrador')->assertSee('Clases con empalme');
    }
}
