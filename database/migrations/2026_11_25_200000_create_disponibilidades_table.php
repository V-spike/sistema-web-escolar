<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * HU-13: espacios (día, franja) en los que un docente puede dar clase.
     * Necesita que ya existan las tablas docentes (T-44) y franjas (T-43).
     */
    public function up(): void
    {
        Schema::create('disponibilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia'); // 1 = lunes … 5 = viernes
            $table->foreignId('franja_id')->constrained('franjas')->cascadeOnDelete();
            $table->timestamps();

            // El mismo espacio no se guarda dos veces para el mismo docente
            $table->unique(['docente_id', 'dia', 'franja_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disponibilidades');
    }
};
