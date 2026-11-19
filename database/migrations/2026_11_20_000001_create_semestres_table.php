<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * HU-07: el periodo escolar (ej. 2026-2027 A). Todo el horario cuelga de aquí.
     */
    public function up(): void
    {
        Schema::create('semestres', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            // Empieza en false: se crea el periodo y después se elige cuál es el activo
            $table->boolean('activo')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semestres');
    }
};
