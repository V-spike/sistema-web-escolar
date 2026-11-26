<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La escuela no usa bloques comunes: Currículum Ampliado se acomoda como clase normal en cada grupo.
 * - Se quita la tabla bloques_comunes.
 * - El tipo de materia "bloque_comun" pasa a llamarse "acreditable": se califica con A o N/A.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('bloques_comunes');

        // 1) Permitir los dos nombres  2) cambiar los datos  3) dejar solo el nombre nuevo
        $this->tipos(['normal', 'bloque_comun', 'acreditable', 'extendido', 'tutoria']);
        DB::table('materias')->where('tipo', 'bloque_comun')->update(['tipo' => 'acreditable']);
        $this->tipos(['normal', 'acreditable', 'extendido', 'tutoria']);
    }

    public function down(): void
    {
        $this->tipos(['normal', 'bloque_comun', 'acreditable', 'extendido', 'tutoria']);
        DB::table('materias')->where('tipo', 'acreditable')->update(['tipo' => 'bloque_comun']);
        $this->tipos(['normal', 'bloque_comun', 'extendido', 'tutoria']);

        Schema::create('bloques_comunes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semestre_id')->constrained('semestres')->cascadeOnDelete();
            $table->unsignedTinyInteger('grado');
            $table->foreignId('materia_id')->constrained('materias');
            $table->unsignedTinyInteger('dia');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
        });
    }

    private function tipos(array $tipos): void
    {
        Schema::table('materias', function (Blueprint $table) use ($tipos) {
            $table->enum('tipo', $tipos)->default('normal')->change();
        });
    }
};
