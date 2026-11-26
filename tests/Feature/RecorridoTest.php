<?php

namespace Tests\Feature;

use App\Models\Aula;
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
 * Recorrido completo: cada rol abre todas sus pantallas y ninguna debe fallar.
 * Si alguien rompe una vista, esta prueba lo avisa.
 */
class RecorridoTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private Clase $clase;
    private Grupo $grupo;
    private Materia $materia;
    private User $alumno;
    private User $docente;

    protected function setUp(): void
    {
        parent::setUp();

        $semestre = Semestre::activo();
        $this->grupo = Grupo::where('letra', 'A')->where('grado', 1)->first();
        $this->materia = Materia::where('clave', 'PM1')->first();
        $this->alumno = User::where('email', 'alumno@escolar.test')->first();
        $this->docente = User::where('email', 'docente@escolar.test')->first();

        // D-001 es la cuenta del docente de prueba; le damos PM1 y una clase en 1.º A
        $d001 = Docente::where('numero_personal', 'D-001')->first();
        $d001->materias()->syncWithoutDetaching([$this->materia->id]);
        $this->clase = Clase::create([
            'semestre_id' => $semestre->id, 'grupo_id' => $this->grupo->id, 'materia_id' => $this->materia->id,
            'docente_id' => $d001->id, 'aula_id' => $this->grupo->aula_id, 'dia' => 1, 'hora_inicio' => '15:00', 'hora_fin' => '15:50',
        ]);

        Calificacion::create(['semestre_id' => $semestre->id, 'alumno_id' => $this->alumno->id,
            'materia_id' => $this->materia->id, 'parcial' => 1, 'valor' => '8.5']);
    }

    public function test_el_superusuario_abre_todas_las_pantallas(): void
    {
        $su = User::where('rol', 'superusuario')->first();
        $admin = User::where('rol', 'administrador')->first();
        $docente = Docente::first();

        $paginas = [
            '/panel/superusuario', '/cambiar-contrasena',
            '/panel/administradores', '/panel/administradores/create', "/panel/administradores/{$admin->id}/edit",
            '/panel/horarios', '/panel/horarios?vista=docente', '/panel/horarios?vista=aula', "/panel/horarios/{$this->clase->id}/editar",
            '/panel/calificaciones', "/panel/calificaciones/{$this->grupo->id}/{$this->materia->id}?parcial=2",
            "/panel/informes/{$this->grupo->id}?parcial=1", "/panel/boletas/grupo/{$this->grupo->id}", "/panel/boletas/alumno/{$this->alumno->id}",
            '/panel/semestres', '/panel/semestres/create', '/panel/semestres/' . Semestre::first()->id . '/edit',
            '/panel/jornada',
            '/panel/materias', '/panel/materias/create', "/panel/materias/{$this->materia->id}/edit",
            '/panel/aulas', '/panel/aulas/create', '/panel/aulas/' . Aula::first()->id . '/edit',
            '/panel/docentes', '/panel/docentes?orden=horas_desc&estado=activos&buscar=a', '/panel/docentes/create',
            "/panel/docentes/{$docente->id}/edit", "/panel/docentes/{$docente->id}/disponibilidad",
            '/panel/grupos', '/panel/grupos/create', "/panel/grupos/{$this->grupo->id}/edit",
            '/panel/alumnos', "/panel/alumnos?grupo={$this->grupo->id}", '/panel/alumnos/create', "/panel/alumnos/{$this->alumno->id}/edit",
        ];

        $this->actingAs($su);
        foreach ($paginas as $pagina) {
            $this->get($pagina)->assertOk();
        }
    }

    public function test_el_administrador_abre_su_inicio(): void
    {
        $this->actingAs(User::where('rol', 'administrador')->first())->get('/panel/administrador')->assertOk();
    }

    public function test_el_docente_abre_sus_pantallas(): void
    {
        $this->actingAs($this->docente);
        foreach (['/panel/docente', '/panel/calificaciones', "/panel/calificaciones/{$this->grupo->id}/{$this->materia->id}",
                  "/panel/boletas/grupo/{$this->grupo->id}", '/cambiar-contrasena'] as $pagina) {
            $this->get($pagina)->assertOk();
        }
    }

    public function test_el_alumno_abre_sus_pantallas(): void
    {
        $this->actingAs($this->alumno);
        foreach (['/panel/alumno', '/panel/mis-calificaciones', '/cambiar-contrasena'] as $pagina) {
            $this->get($pagina)->assertOk();
        }
    }

    public function test_sin_sesion_solo_se_ve_el_acceso(): void
    {
        foreach (['/login', '/olvide-contrasena', '/restablecer/algo?email=x@escolar.test'] as $pagina) {
            $this->get($pagina)->assertOk();
        }
        $this->get('/panel/calificaciones')->assertRedirect('/login');
    }

    public function test_un_docente_o_alumno_sin_grupo_ni_clases_no_rompe_nada(): void
    {
        Clase::query()->delete();
        $this->alumno->update(['grupo_id' => null]);

        $this->actingAs($this->docente)->get('/panel/calificaciones')->assertOk()->assertSee('Todavía no tienes clases');
        $this->actingAs($this->alumno)->get('/panel/mis-calificaciones')->assertOk()->assertSee('no tienes grupo');
        $this->get('/panel/alumno')->assertOk();
    }
}
