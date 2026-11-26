<?php

namespace Tests\Feature;

use App\Models\Clase;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Alumnos, administradores y "Mi horario" de docentes y alumnos.
 */
class CuentasTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function usuario(string $rol): User
    {
        return User::where('rol', $rol)->where('activo', true)->first();
    }

    public function test_con_sesion_abierta_el_login_manda_a_su_panel(): void
    {
        foreach (['superusuario', 'administrador', 'docente', 'alumno'] as $rol) {
            $this->actingAs($this->usuario($rol))->get('/login')->assertRedirect('/panel/'.$rol);
        }
    }

    public function test_registrar_un_alumno_en_un_grupo(): void
    {
        $grupo = Grupo::where('letra', 'B')->first();

        $this->actingAs($this->usuario('administrador'))->post('/panel/alumnos', [
            'name' => 'Luis Pérez', 'matricula' => 'A-0100', 'email' => 'luis@escolar.test', 'grupo_id' => $grupo->id,
            'password' => 'Secreta2026', 'password_confirmation' => 'Secreta2026',
        ])->assertRedirect('/panel/alumnos');

        $alumno = User::where('email', 'luis@escolar.test')->first();
        $this->assertSame('alumno', $alumno->rol);
        $this->assertSame($grupo->id, $alumno->grupo_id);
        $this->actingAs($this->usuario('administrador'))->get('/panel/alumnos')->assertOk()->assertSee('Luis Pérez');
    }

    public function test_la_matricula_no_se_repite(): void
    {
        User::where('email', 'alumno@escolar.test')->update(['matricula' => 'A-0001']);

        $this->actingAs($this->usuario('administrador'))->post('/panel/alumnos', [
            'name' => 'Otro', 'matricula' => 'A-0001', 'email' => 'otro@escolar.test', 'grupo_id' => Grupo::first()->id,
            'password' => 'Secreta2026', 'password_confirmation' => 'Secreta2026',
        ])->assertSessionHasErrors('matricula');
    }

    public function test_solo_el_superusuario_gestiona_administradores(): void
    {
        $this->actingAs($this->usuario('administrador'))->get('/panel/administradores')->assertForbidden();

        $this->actingAs($this->usuario('superusuario'))->post('/panel/administradores', [
            'name' => 'Nueva Admin', 'email' => 'nueva@escolar.test', 'password' => 'Secreta2026', 'password_confirmation' => 'Secreta2026',
        ])->assertRedirect('/panel/administradores');

        $this->assertSame('administrador', User::where('email', 'nueva@escolar.test')->first()->rol);
    }

    public function test_mi_horario_del_docente_y_del_alumno(): void
    {
        $grupo = Grupo::where('letra', 'A')->first();
        $docente = Docente::where('numero_personal', 'D-008')->first();
        $docente->update(['user_id' => $this->usuario('docente')->id]);
        User::where('email', 'alumno@escolar.test')->update(['grupo_id' => $grupo->id]);

        Clase::create([
            'semestre_id' => Semestre::activo()->id, 'grupo_id' => $grupo->id,
            'materia_id' => Materia::where('clave', 'PM1')->first()->id, 'docente_id' => $docente->id,
            'aula_id' => $grupo->aula_id, 'dia' => 2, 'hora_inicio' => '15:00', 'hora_fin' => '15:50',
        ]);

        $this->actingAs($this->usuario('docente'))->get('/panel/docente')->assertOk()->assertSee('Mi horario')->assertSee('P.M1')->assertSee('1.º A');
        $this->actingAs(User::where('email', 'alumno@escolar.test')->first())->get('/panel/alumno')->assertOk()->assertSee('P.M1');
    }
}
