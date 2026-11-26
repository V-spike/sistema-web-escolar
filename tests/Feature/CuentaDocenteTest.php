<?php

namespace Tests\Feature;

use App\Models\Docente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * El administrador le crea al docente su cuenta para entrar a "Mi horario".
 */
class CuentaDocenteTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    public function test_crear_la_cuenta_de_un_docente(): void
    {
        $docente = Docente::whereNull('user_id')->first();

        $this->actingAs($this->admin())->get("/panel/docentes/{$docente->id}/edit")->assertSee('Crear cuenta');

        $this->post("/panel/docentes/{$docente->id}/cuenta", [
            'email' => 'nuevo.docente@escolar.test', 'password' => 'Escolar2026', 'password_confirmation' => 'Escolar2026',
        ])->assertRedirect("/panel/docentes/{$docente->id}/edit");

        $cuenta = $docente->fresh()->usuario;
        $this->assertSame('docente', $cuenta->rol);
        $this->assertSame($docente->nombre_completo, $cuenta->name);

        // Con esa cuenta ya puede entrar a su horario
        $this->post('/logout');
        $this->post('/login', ['email' => 'nuevo.docente@escolar.test', 'password' => 'Escolar2026'])
            ->assertRedirect('/panel/docente');
    }

    public function test_al_crearla_la_contrasena_es_obligatoria_y_el_correo_no_se_repite(): void
    {
        $docente = Docente::whereNull('user_id')->first();

        $this->actingAs($this->admin())->post("/panel/docentes/{$docente->id}/cuenta", [
            'email' => 'alumno@escolar.test',
        ])->assertSessionHasErrors(['email', 'password']);

        $this->assertNull($docente->fresh()->user_id);
    }

    public function test_cambiar_el_correo_sin_tocar_la_contrasena(): void
    {
        $docente = Docente::whereNull('user_id')->first();
        $this->actingAs($this->admin())->post("/panel/docentes/{$docente->id}/cuenta", [
            'email' => 'antes@escolar.test', 'password' => 'Escolar2026', 'password_confirmation' => 'Escolar2026',
        ]);

        $this->post("/panel/docentes/{$docente->id}/cuenta", ['email' => 'despues@escolar.test'])->assertSessionHasNoErrors();

        $cuenta = $docente->fresh()->usuario;
        $this->assertSame('despues@escolar.test', $cuenta->email);
        $this->assertTrue(Hash::check('Escolar2026', $cuenta->password));
    }

    public function test_dar_de_baja_al_docente_tambien_bloquea_su_cuenta(): void
    {
        $docente = Docente::whereNull('user_id')->whereDoesntHave('clases')->first();
        $this->actingAs($this->admin())->post("/panel/docentes/{$docente->id}/cuenta", [
            'email' => 'baja.docente@escolar.test', 'password' => 'Escolar2026', 'password_confirmation' => 'Escolar2026',
        ]);

        $this->patch("/panel/docentes/{$docente->id}/estado");
        $this->assertFalse($docente->fresh()->usuario->activo);

        $this->patch("/panel/docentes/{$docente->id}/estado");
        $this->assertTrue($docente->fresh()->usuario->activo);
    }

    public function test_un_docente_no_puede_crear_cuentas(): void
    {
        $docente = Docente::first();

        $this->actingAs(User::where('rol', 'docente')->first())
            ->post("/panel/docentes/{$docente->id}/cuenta", ['email' => 'x@escolar.test'])
            ->assertForbidden();
    }
}
