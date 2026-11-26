<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nuevo tipo de materia "extendido" (componente de formación fundamental extendido, como PyCC).
 * Se acomoda en el horario como una materia normal, pero no lleva calificación en la boleta.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->enum('tipo', ['normal', 'bloque_comun', 'extendido', 'tutoria'])->default('normal')->change();
        });
    }

    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->enum('tipo', ['normal', 'bloque_comun', 'tutoria'])->default('normal')->change();
        });
    }
};
