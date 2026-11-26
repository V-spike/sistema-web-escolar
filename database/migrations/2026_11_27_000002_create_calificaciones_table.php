<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Una fila por alumno, materia y parcial.
 * "valor" es texto porque además de números (5 a 10) se usa NP, y en bloque común A o N/A.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->foreignId('alumno_id')->constrained('users');
            $table->foreignId('materia_id')->constrained('materias');
            $table->unsignedTinyInteger('parcial');      // 1, 2 o 3
            $table->string('valor', 4);                   // "8.5", "10", "NP", "A", "N/A"
            $table->timestamps();

            // Un alumno tiene una sola calificación por materia y parcial
            $table->unique(['semestre_id', 'alumno_id', 'materia_id', 'parcial'], 'calificacion_unica');
        });

        // La boleta lleva la CURP del alumno
        Schema::table('users', function (Blueprint $table) {
            $table->string('curp', 18)->nullable()->unique()->after('matricula');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['curp']);
            $table->dropColumn('curp');
        });
    }
};
