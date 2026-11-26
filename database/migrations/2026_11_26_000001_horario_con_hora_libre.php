<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sprint 6: el horario se acomoda a mano con hora libre de inicio y fin.
     *
     * - Se quitan las franjas fijas (T-43). La jornada pasa a ser una hora de entrada,
     *   una hora de salida y una lista de recesos.
     * - Disponibilidad (T-46) y bloques comunes (T-49) se guardan como rangos de hora.
     * - Nueva tabla clases: cada clase de un grupo con su materia, docente, aula, día y horas.
     */
    public function up(): void
    {
        Schema::dropIfExists('disponibilidades');
        Schema::dropIfExists('bloques_comunes');
        Schema::dropIfExists('franjas');

        // Una sola fila: el horario del turno
        Schema::create('jornadas', function (Blueprint $table) {
            $table->id();
            $table->time('hora_entrada');
            $table->time('hora_salida');
            $table->unsignedTinyInteger('minutos_por_hora')->default(50); // una "hora clase"
            $table->timestamps();
        });

        Schema::create('recesos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 40);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
        });

        Schema::create('disponibilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia'); // 1 = lunes … 5 = viernes
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();

            $table->index(['docente_id', 'dia']);
        });

        Schema::create('bloques_comunes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semestre_id')->constrained('semestres')->cascadeOnDelete();
            $table->unsignedTinyInteger('grado');
            $table->foreignId('materia_id')->constrained('materias');
            $table->unsignedTinyInteger('dia');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();

            $table->index(['semestre_id', 'grado', 'dia']);
        });

        Schema::create('clases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semestre_id')->constrained('semestres')->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('docente_id')->constrained('docentes');
            $table->foreignId('aula_id')->nullable()->constrained('aulas')->nullOnDelete();
            $table->unsignedTinyInteger('dia');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();

            // Para buscar rápido los empalmes de cada lado
            $table->index(['semestre_id', 'grupo_id', 'dia']);
            $table->index(['semestre_id', 'docente_id', 'dia']);
            $table->index(['semestre_id', 'aula_id', 'dia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clases');
        Schema::dropIfExists('bloques_comunes');
        Schema::dropIfExists('disponibilidades');
        Schema::dropIfExists('recesos');
        Schema::dropIfExists('jornadas');
        // Las franjas no se reconstruyen: el modelo de franjas fijas se retiró.
    }
};
