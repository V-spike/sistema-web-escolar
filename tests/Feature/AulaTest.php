<?php

namespace Tests\Feature;

use App\Models\Aula;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AulaTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    public function test_la_lista_de_aulas_carga(): void
    {
        $this->actingAs($this->admin())
            ->get('/panel/aulas')
            ->assertOk()
            ->assertSee('LAB-C');
    }

    public function test_registrar_un_aula(): void
    {
        $this->actingAs($this->admin())
            ->post('/panel/aulas', ['clave' => 'B-201', 'edificio' => 'Edificio B', 'tipo' => 'salon', 'capacidad' => 30])
            ->assertRedirect('/panel/aulas');

        $this->assertDatabaseHas('aulas', ['clave' => 'B-201', 'tipo' => 'salon']);
    }

    public function test_no_acepta_un_tipo_invalido(): void
    {
        $this->actingAs($this->admin())
            ->post('/panel/aulas', ['clave' => 'B-202', 'edificio' => 'Edificio B', 'tipo' => 'cancha', 'capacidad' => 30])
            ->assertSessionHasErrors('tipo');
    }

    public function test_dar_de_baja_y_reactivar(): void
    {
        $aula = Aula::where('clave', 'S-1')->first();

        $this->actingAs($this->admin())->patch("/panel/aulas/{$aula->id}/estado");
        $this->assertFalse($aula->fresh()->activo);

        $this->actingAs($this->admin())->patch("/panel/aulas/{$aula->id}/estado");
        $this->assertTrue($aula->fresh()->activo);
    }
}
