<?php

namespace Tests\Feature;

use App\Models\Disponibilidad;
use App\Models\Docente;
use App\Models\Franja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisponibilidadTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('rol', 'administrador')->first();
    }

    private function docente(): Docente
    {
        return Docente::create([
            'numero_personal' => 'D-999',
            'nombre' => 'Ana',
            'apellido_paterno' => 'Prueba',
            'horas_plaza' => 20,
        ]);
    }

    public function test_se_ve_la_cuadricula(): void
    {
        $docente = $this->docente();

        $this->actingAs($this->admin())
            ->get("/panel/docentes/{$docente->id}/disponibilidad")
            ->assertOk()
            ->assertSee('Disponibilidad de Ana');
    }

    public function test_guardar_reemplaza_lo_anterior(): void
    {
        $docente = $this->docente();
        $clases = Franja::clases()->orderBy('orden')->get();

        $this->actingAs($this->admin())
            ->put("/panel/docentes/{$docente->id}/disponibilidad", [
                'espacios' => ["1-{$clases[0]->id}", "2-{$clases[1]->id}"],
            ])
            ->assertRedirect("/panel/docentes/{$docente->id}/disponibilidad");

        $this->assertSame(2, Disponibilidad::where('docente_id', $docente->id)->count());

        // Segunda vez con un solo espacio: deben quedar solo 1
        $this->actingAs($this->admin())
            ->put("/panel/docentes/{$docente->id}/disponibilidad", [
                'espacios' => ["3-{$clases[2]->id}"],
            ]);

        $this->assertSame(1, Disponibilidad::where('docente_id', $docente->id)->count());
    }

    public function test_un_receso_no_se_guarda(): void
    {
        $docente = $this->docente();
        $receso = Franja::where('es_receso', true)->first();

        $this->actingAs($this->admin())
            ->put("/panel/docentes/{$docente->id}/disponibilidad", [
                'espacios' => ["1-{$receso->id}"],
            ]);

        $this->assertSame(0, Disponibilidad::where('docente_id', $docente->id)->count());
    }

    public function test_un_valor_raro_se_rechaza(): void
    {
        $docente = $this->docente();

        $this->actingAs($this->admin())
            ->put("/panel/docentes/{$docente->id}/disponibilidad", ['espacios' => ['9-hola']])
            ->assertSessionHasErrors('espacios.0');
    }

    public function test_sin_sesion_manda_al_login(): void
    {
        $docente = $this->docente();

        $this->get("/panel/docentes/{$docente->id}/disponibilidad")->assertRedirect('/login');
    }
}
