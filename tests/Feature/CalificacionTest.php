<?php

namespace Tests\Feature;

use App\Calificaciones\Letra;
use App\Models\Calificacion;
use App\Models\Clase;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Captura de calificaciones por parcial, informe del grupo y boleta.
 * Los alumnos de ejemplo vienen de AlumnosSeeder (15 por grupo, inventados).
 */
class CalificacionTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private Grupo $grupoA;
    private Materia $pm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->grupoA = Grupo::where('letra', 'A')->where('grado', 1)->first();
        $this->pm = Materia::where('clave', 'PM1')->first();
    }

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    /** El docente de prueba queda como D-001 y da Pensamiento Matemático en 1.º A */
    private function docenteConClase(): User
    {
        $usuario = User::where('email', 'docente@escolar.test')->first();
        $docente = Docente::where('numero_personal', 'D-001')->first();
        $docente->update(['user_id' => $usuario->id]);

        Clase::create([
            'semestre_id' => Semestre::activo()->id, 'grupo_id' => $this->grupoA->id, 'materia_id' => $this->pm->id,
            'docente_id' => $docente->id, 'aula_id' => null, 'dia' => 1, 'hora_inicio' => '15:00', 'hora_fin' => '15:50',
        ]);

        return $usuario;
    }

    private function url(?Grupo $grupo = null, ?Materia $materia = null): string
    {
        return '/panel/calificaciones/' . ($grupo ?? $this->grupoA)->id . '/' . ($materia ?? $this->pm)->id;
    }

    public function test_el_docente_solo_ve_y_califica_donde_da_clase(): void
    {
        $docente = $this->docenteConClase();

        $this->actingAs($docente)->get('/panel/calificaciones')->assertOk()
            ->assertSee('1.º A')->assertSee('Pensamiento Matemático I')->assertDontSee('Inglés I');

        $this->get($this->url())->assertOk();

        // Otra materia u otro grupo: no
        $this->get($this->url(null, Materia::where('clave', 'ING1')->first()))->assertForbidden();
        $this->get($this->url(Grupo::where('letra', 'B')->first()))->assertForbidden();
    }

    public function test_guardar_un_parcial_y_limpiar_lo_escrito(): void
    {
        $docente = $this->docenteConClase();
        [$a1, $a2, $a3] = $this->grupoA->alumnos()->orderBy('name')->take(3)->get();

        $this->actingAs($docente)->put($this->url(), [
            'parcial' => 2,
            'valores' => [$a1->id => '8,5', $a2->id => 'np', $a3->id => '10.0'],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $guardado = fn ($a) => Calificacion::where('alumno_id', $a->id)->where('materia_id', $this->pm->id)->where('parcial', 2)->value('valor');
        $this->assertSame('8.5', $guardado($a1));
        $this->assertSame('NP', $guardado($a2));
        $this->assertSame('10', $guardado($a3));

        // Dejarlo vacío quita la calificación
        $this->put($this->url(), ['parcial' => 2, 'valores' => [$a1->id => '']]);
        $this->assertNull($guardado($a1));
    }

    public function test_si_una_calificacion_esta_mal_no_se_guarda_ninguna(): void
    {
        $docente = $this->docenteConClase();
        [$a1, $a2] = $this->grupoA->alumnos()->orderBy('name')->take(2)->get();

        $this->actingAs($docente)->put($this->url(), [
            'parcial' => 1,
            'valores' => [$a1->id => '9', $a2->id => '11'],
        ])->assertSessionHasErrors("valores.{$a2->id}");

        $this->assertSame(0, Calificacion::count());

        foreach (['4', 'abc', '8.55'] as $malo) {
            $this->put($this->url(), ['parcial' => 1, 'valores' => [$a1->id => $malo]])->assertSessionHasErrors();
        }
    }

    public function test_curriculum_ampliado_se_califica_con_a_o_na(): void
    {
        $ca = Materia::where('clave', 'CA1')->first();
        $alumno = $this->grupoA->alumnos()->first();

        $this->actingAs($this->admin())->put($this->url(null, $ca), ['parcial' => 1, 'valores' => [$alumno->id => '9']])
            ->assertSessionHasErrors();

        $this->put($this->url(null, $ca), ['parcial' => 1, 'valores' => [$alumno->id => 'n/a']])->assertSessionHasNoErrors();
        $this->assertSame('N/A', Calificacion::where('alumno_id', $alumno->id)->value('valor'));
    }

    public function test_la_boleta_calcula_promedio_final_y_letra(): void
    {
        $alumno = User::where('email', 'alumno@escolar.test')->first();
        $semestre = Semestre::activo();

        foreach ([1 => '8', 2 => '9', 3 => 'NP'] as $parcial => $valor) {
            Calificacion::create(['semestre_id' => $semestre->id, 'alumno_id' => $alumno->id,
                'materia_id' => $this->pm->id, 'parcial' => $parcial, 'valor' => $valor]);
        }

        // (8 + 9 + 5) / 3 = 7.3 → "SIETE PUNTO TRES" en la boleta que imprime la escuela
        $this->actingAs($this->admin())->get("/panel/boletas/alumno/{$alumno->id}")->assertOk()
            ->assertSee('Boleta interna y provisional de calificaciones')
            ->assertSee('PRUE100101HVZRBLA0')
            ->assertSee('PENSAMIENTO MATEMÁTICO I')
            ->assertSee('SIETE PUNTO TRES')
            ->assertSee('3P')
            ->assertSee('30EBH0389X');
    }

    public function test_el_alumno_ve_sus_calificaciones_por_materia_sin_la_boleta(): void
    {
        $alumno = User::where('email', 'alumno@escolar.test')->first();
        $semestre = Semestre::activo();
        foreach ([1 => '8', 2 => '9.5'] as $parcial => $valor) {
            Calificacion::create(['semestre_id' => $semestre->id, 'alumno_id' => $alumno->id,
                'materia_id' => $this->pm->id, 'parcial' => $parcial, 'valor' => $valor]);
        }

        $this->actingAs($alumno)->get('/panel/mis-calificaciones')->assertOk()
            ->assertSee('Mis calificaciones')->assertSee('Pensamiento Matemático I')->assertSee('9.5')
            ->assertDontSee('Boleta interna y provisional de calificaciones');

        // La boleta para imprimir no es para el alumno
        $this->get("/panel/boletas/alumno/{$alumno->id}")->assertForbidden();
    }

    public function test_el_docente_imprime_las_boletas_solo_de_sus_grupos(): void
    {
        $docente = $this->docenteConClase();

        $this->actingAs($docente)->get("/panel/boletas/grupo/{$this->grupoA->id}")->assertOk()
            ->assertSee('Boleta interna y provisional de calificaciones');
        $this->get('/panel/boletas/grupo/' . Grupo::where('letra', 'B')->first()->id)->assertForbidden();
    }

    public function test_informe_y_boletas_del_grupo_son_solo_de_la_administracion(): void
    {
        $this->actingAs($this->admin())->get("/panel/informes/{$this->grupoA->id}?parcial=1")->assertOk()
            ->assertSee('Informe de calificaciones')->assertSee('P.M1');
        $this->get("/panel/boletas/grupo/{$this->grupoA->id}")->assertOk();

        $alumno = User::where('email', 'alumno@escolar.test')->first();
        $this->actingAs($alumno)->get("/panel/informes/{$this->grupoA->id}")->assertForbidden();
        $this->get('/panel/calificaciones')->assertForbidden();
    }

    public function test_el_informe_se_descarga_para_excel(): void
    {
        $alumno = User::where('email', 'alumno@escolar.test')->first();
        Calificacion::create(['semestre_id' => Semestre::activo()->id, 'alumno_id' => $alumno->id,
            'materia_id' => $this->pm->id, 'parcial' => 1, 'valor' => '9.5']);

        $respuesta = $this->actingAs($this->admin())->get("/panel/informes/{$this->grupoA->id}?parcial=1&formato=excel");

        $respuesta->assertOk()->assertDownload('informe-1A-parcial1.csv');
        $contenido = $respuesta->streamedContent();
        $this->assertStringContainsString('INFORME DE CALIFICACIONES', $contenido);
        $this->assertStringContainsString('P.M1', $contenido);
        $this->assertStringContainsString('ALUMNO DE PRUEBA', $contenido);
        $this->assertStringContainsString('9.5', $contenido);
    }

    public function test_letra_de_una_calificacion(): void
    {
        $this->assertSame('OCHO PUNTO CINCO', Letra::de(8.5));
        $this->assertSame('DIEZ', Letra::de(10));
        $this->assertSame('', Letra::de(null));
    }
}
