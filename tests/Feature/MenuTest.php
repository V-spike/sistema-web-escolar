<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_el_administrador_ve_los_enlaces_del_sprint_5(): void
    {
        $admin = User::where('rol', 'administrador')->first();

        $respuesta = $this->actingAs($admin)->get('/panel/administrador')->assertOk();

        foreach (['jornada', 'bloques-comunes', 'docentes', 'grupos', 'semestres', 'materias', 'aulas'] as $seccion) {
            $respuesta->assertSee('/panel/' . $seccion, false);
        }
    }

    public function test_el_docente_no_ve_los_catalogos(): void
    {
        $docente = User::where('rol', 'docente')->first();

        $this->actingAs($docente)
            ->get('/panel/docente')
            ->assertOk()
            ->assertDontSee('/panel/docentes', false)
            ->assertDontSee('/panel/grupos', false);
    }
}
