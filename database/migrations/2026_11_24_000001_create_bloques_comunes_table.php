<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * HU-25: franjas que todos los grupos de un grado tienen a la vez
     * (ej. Currículum Ampliado de 1.º, lunes 17:50 a 19:30). El generador no las mueve.
     */
    public function up(): void
    {
        Schema::create('bloques_comunes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semestre_id')->constrained('semestres')->cascadeOnDelete();
            $table->unsignedTinyInteger('grado'); // 1 a 6
            $table->foreignId('materia_id')->constrained('materias');
            $table->unsignedTinyInteger('dia'); // 1 = lunes … 5 = viernes
            $table->foreignId('franja_id')->constrained('franjas');
            $table->timestamps();

            // Un grado no puede tener dos bloques en el mismo día y franja del mismo periodo
            $table->unique(['semestre_id', 'grado', 'dia', 'franja_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloques_comunes');
    }
};
