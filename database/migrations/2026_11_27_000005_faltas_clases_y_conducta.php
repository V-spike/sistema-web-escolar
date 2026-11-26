<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lo que la boleta pedía y no se capturaba:
 * - faltas del alumno en cada materia y parcial (van junto a la calificación)
 * - clases impartidas de cada materia, por grupo y parcial
 * - conducta del alumno en el semestre (Buena, Regular o Mala)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calificaciones', function (Blueprint $table) {
            $table->string('valor', 4)->nullable()->change();          // puede haber faltas sin calificación todavía
            $table->unsignedSmallInteger('faltas')->nullable()->after('valor');
        });

        Schema::create('clases_impartidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->foreignId('materia_id')->constrained('materias');
            $table->unsignedTinyInteger('parcial');
            $table->unsignedSmallInteger('total');
            $table->timestamps();

            $table->unique(['semestre_id', 'grupo_id', 'materia_id', 'parcial'], 'impartidas_unica');
        });

        Schema::create('conductas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->foreignId('alumno_id')->constrained('users');
            $table->enum('valor', ['buena', 'regular', 'mala']);
            $table->timestamps();

            $table->unique(['semestre_id', 'alumno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conductas');
        Schema::dropIfExists('clases_impartidas');

        Schema::table('calificaciones', function (Blueprint $table) {
            $table->dropColumn('faltas');
        });
    }
};
