<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Login, cierre de sesión, recuperar y cambiar contraseña.
 * Las cuentas y la contraseña "Escolar2026" vienen de UsuariosSeeder.
 */
class AccesoTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_cada_rol_entra_a_su_panel(): void
    {
        foreach (['superusuario', 'administrador', 'docente', 'alumno'] as $rol) {
            $this->post('/login', ['email' => "{$rol}@escolar.test", 'password' => 'Escolar2026'])
                ->assertRedirect("/panel/{$rol}");
            $this->post('/logout');
        }
    }

    public function test_contrasena_incorrecta(): void
    {
        $this->post('/login', ['email' => 'alumno@escolar.test', 'password' => 'otra'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_una_cuenta_dada_de_baja_no_entra(): void
    {
        $this->post('/login', ['email' => 'baja@escolar.test', 'password' => 'Escolar2026'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_si_la_dan_de_baja_con_sesion_abierta_la_saca(): void
    {
        $alumno = User::where('email', 'alumno@escolar.test')->first();
        $this->actingAs($alumno)->get('/panel/alumno')->assertOk();

        $alumno->update(['activo' => false]);

        $this->get('/panel/alumno')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_limite_de_intentos_en_el_login(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'alumno@escolar.test', 'password' => 'mal']);
        }

        $this->post('/login', ['email' => 'alumno@escolar.test', 'password' => 'Escolar2026'])
            ->assertStatus(429);
    }

    public function test_cerrar_sesion(): void
    {
        $this->actingAs(User::where('rol', 'docente')->first())
            ->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_un_rol_no_entra_al_panel_de_otro(): void
    {
        $this->actingAs(User::where('rol', 'alumno')->first())
            ->get('/panel/administrador')
            ->assertForbidden()
            ->assertSee('No tienes permiso');
    }

    public function test_pagina_no_encontrada_en_espanol(): void
    {
        $this->get('/no-existe')->assertNotFound()->assertSee('Página no encontrada');
    }

    public function test_recuperar_contrasena_manda_el_enlace(): void
    {
        Notification::fake();
        $alumno = User::where('email', 'alumno@escolar.test')->first();

        $this->post('/olvide-contrasena', ['email' => $alumno->email])->assertSessionHas('estado');

        Notification::assertSentTo($alumno, ResetPassword::class);
    }

    public function test_correo_no_registrado_da_el_mismo_aviso(): void
    {
        Notification::fake();

        $this->post('/olvide-contrasena', ['email' => 'nadie@escolar.test'])->assertSessionHas('estado');

        Notification::assertNothingSent();
    }

    public function test_restablecer_con_el_enlace(): void
    {
        $alumno = User::where('email', 'alumno@escolar.test')->first();
        $token = Password::createToken($alumno);

        $this->get("/restablecer/{$token}?email={$alumno->email}")->assertOk();

        $this->post('/restablecer', [
            'token' => $token, 'email' => $alumno->email,
            'password' => 'NuevaClave1', 'password_confirmation' => 'NuevaClave1',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('NuevaClave1', $alumno->fresh()->password));
    }

    public function test_un_enlace_falso_no_sirve(): void
    {
        $this->post('/restablecer', [
            'token' => 'inventado', 'email' => 'alumno@escolar.test',
            'password' => 'NuevaClave1', 'password_confirmation' => 'NuevaClave1',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('Escolar2026', User::where('email', 'alumno@escolar.test')->first()->password));
    }

    public function test_cambiar_contrasena(): void
    {
        $docente = User::where('rol', 'docente')->first();

        $this->actingAs($docente)->post('/cambiar-contrasena', [
            'password_actual' => 'equivocada', 'password' => 'NuevaClave1', 'password_confirmation' => 'NuevaClave1',
        ])->assertSessionHasErrors('password_actual');

        $this->post('/cambiar-contrasena', [
            'password_actual' => 'Escolar2026', 'password' => 'NuevaClave1', 'password_confirmation' => 'NuevaClave1',
        ])->assertSessionHas('estado');

        $this->assertTrue(Hash::check('NuevaClave1', $docente->fresh()->password));
    }
}
